<?php

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2021 Sjoerd Zonneveld  <code@bitpatroon.nl>
 *  Date: 29-4-2021 13:23
 *
 *  All rights reserved
 *
 *  This script is part of a Bitpatroon project. The project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

namespace BPN\BpnRequestAccess\Controller;

use BPN\BpnRequestAccess\Configuration\BpnRequestAccessConfiguration;
use BPN\BpnRequestAccess\Domain\Form\AccessForm;
use BPN\BpnRequestAccess\Domain\Form\DenyAccessFeedbackForm;
use BPN\BpnRequestAccess\Domain\Model\Request;
use BPN\BpnRequestAccess\Domain\Repository\FrontendUserGroupRepository;
use BPN\BpnRequestAccess\Service\AccessService;
use BPN\BpnRequestAccess\Service\AuthorizationService;
use BPN\BpnRequestAccess\Service\EmailService;
use BPN\BpnRequestAccess\Service\ExpiringGroupsService;
use BPN\BpnVariableText\Service\TextService;
use BPN\Configuration\Configuration\ExtensionConfigurationManager;
use TYPO3\CMS\Core\Domain\Repository\PageRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Domain\Model\FrontendUser;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Property\TypeConverter\DateTimeConverter;

class RequestAccessController extends ActionController
{
    const ADMIN_ALLOW_ACCESS = '1';
    const ADMIN_DENY_ACCESS = '-1';

    /**
     * @var BpnRequestAccessConfiguration
     */
    protected $accessConfiguration;

    /**
     * @var PageRepository
     */
    protected $pageRepository;

    /**
     * @var AuthorizationService
     */
    protected $authorizationService;

    /**
     * @var EmailService
     */
    protected $emailService;
    /**
     * @var FrontendUserGroupRepository
     */
    private $frontendUserGroupRepository;

    /**
     * Initializer
     * Creates FrontendUserGroup repository with configured storage page set.
     */
    public function initializeObject()
    {
        /** @var BpnRequestAccessConfiguration $allowAccessConfiguration */
        $allowAccessConfiguration = ExtensionConfigurationManager::getConfigurationStatic();

        // set default mail settings
        $this->emailService->disableSendMail($allowAccessConfiguration->getDoNotSendMail());
    }

    public function initializeRequestAccessAction()
    {
        $dateFormat = 'd-m-Y';
        /** @var \TYPO3\CMS\Extbase\Mvc\Controller\MvcPropertyMappingConfiguration $propertyMappingConfiguration */
        $propertyMappingConfiguration = $this->arguments['accessForm']->getPropertyMappingConfiguration();
        $propertyMappingConfiguration->forProperty('start')
            ->setTypeConverterOption(
                DateTimeConverter::class,
                DateTimeConverter::CONFIGURATION_DATE_FORMAT,
                $dateFormat
            );
        $propertyMappingConfiguration->allowAllProperties();
        $propertyMappingConfiguration->allowProperties('user');
    }

    /**
     * Doesn't show anything.
     */
    public function indexAction()
    {
    }

    public function requestAccessFormAction(AccessForm $accessForm = null)
    {
        $this->ensureAuthorized();

        try {
            if (null === $accessForm) {
                $accessForm = new AccessForm();
                $accessForm->setUser($this->authorizationService->getFrontendUser());
            }

            $data = [];
            $data['start'] = date('d-m-Y');
            $data['usergroups'] = $this->toAssocArray(
                $this->frontendUserGroupRepository->findByIdentifiers(
                    GeneralUtility::intExplode(',', $this->settings['usergroups'])
                ),
                'title',
                'uid'
            );
            $data['permittedDuration'] = $this->toAssocArray(
                $this->accessConfiguration->getPermittedDurations()
            );
            $this->view->assign('accessForm', $accessForm);
            $this->view->assign('data', $data);
        } catch (\Exception $exception) {
            $this->handleException($exception);
        }
    }

    /**
     * Gives the access to the selected user.
     */
    public function requestAccessAction(AccessForm $accessForm)
    {
        $this->ensureAuthorized();

        /** @var AccessService $accessService */
        $accessService = GeneralUtility::makeInstance(ObjectManager::class)
            ->get(AccessService::class);

        if (!$accessForm->getUser()) {
            $accessForm->setUser($this->getAuthorizationService()->getFrontendUser());
        }

        if (!GeneralUtility::validEmail($accessForm->getUser()->getEmail())) {
            $this->redirect('requestAccessForm', null, null, ['userInvalidEmail' => true]);
        }

        if (!$accessService->validateAccessRequestData($accessForm)) {
            $this->forward(
                'invalidRequest',
                null,
                null,
                ['code' => AccessService::RESULT_REQUEST_FORM_VALIDATE_ERROR]
            );

            return;
        }

        try {
            $allowAccessConfiguration = $this->getConfiguration();
            $authorizationService = $this->getAuthorizationService();

            /** @var FrontendUser $userRequestSource */
            $userRequestSource = $authorizationService->getFrontendUser();
            $request = $accessService->createAccessRequest(
                $accessForm->getUser(),
                $userRequestSource,
                $accessForm->getUserGroup(),
                $accessForm->getStart(),
                $accessForm->getPermittedDuration()
            );

            $this->sendRequestAccessEmail($request, $allowAccessConfiguration);
        } catch (\Exception $exception) {
            $this->handleException($exception);
        }
    }

    public function grantAccessAction($verificationCode)
    {
        /** @var BpnRequestAccessConfiguration $allowAccessConfiguration */
        $allowAccessConfiguration = ExtensionConfigurationManager::getConfigurationStatic();

        try {
            $accessService = $this->getAccessService();
            $request = $accessService->getRequest($verificationCode);
            if (!$accessService->isRequestSuccesful($request)) {
                throw new \RuntimeException((int)$request, 1620315642);
            }
            $result = $accessService->grantAccess($verificationCode, $request);
            if (!$result) {
                throw new \RuntimeException(AccessService::RESULT_REQUEST_NOT_FOUND, 1620315922);
            }

            if ($request->getUserRequestSource()->getUid() !== $request->getUserRequestTarget()->getUid()) {
                $this->sendAccessGrantedEmailToSource($request, $allowAccessConfiguration);
            }
            $this->sendAccessGrantedEmailToTarget($request, $allowAccessConfiguration);
            $this->view->assign('result', $result);
            $this->view->assign('request', $request);
        } catch (\Exception $exception) {
            $this->handleException($exception);
        }
    }

    /**
     * Denies access for the given user to the stored opleidingsgebied.
     *
     * @param DenyAccessFeedbackForm $denyAccessFeedbackForm
     */
    public function denyAccessAction(DenyAccessFeedbackForm $denyAccessFeedbackForm = null)
    {
        $verificationCode = '';
        if (null === $denyAccessFeedbackForm) {
            if ($this->request->hasArgument('verificationCode')) {
                $verificationCode = $this->request->getArgument('verificationCode');
            }
        } else {
            $verificationCode = $denyAccessFeedbackForm->getVerificationCode();
        }

        try {
            if (empty($verificationCode)) {
                throw new \RuntimeException(AccessService::RESULT_REQUEST_NOT_FOUND, 1620315931);
            }
            $accessService = $this->getAccessService();
            $request = $accessService->getRequest($verificationCode);
            if (!$accessService->isRequestSuccesful($request)) {
                throw new \RuntimeException((int)$request, 1620316027);
            }
            $this->view->assign('verificationCode', $verificationCode);
        } catch (\Exception $exception) {
            $this->handleException($exception);
        }
    }

    /**
     * Denies access for the given user to the stored opleidingsgebied.
     */
    public function denyAccessWithFeedbackAction(DenyAccessFeedbackForm $denyAccessFeedbackForm)
    {
        $verificationCode = $denyAccessFeedbackForm->getVerificationCode();

        /** @var BpnRequestAccessConfiguration $accessConfiguration */
        $accessConfiguration = ExtensionConfigurationManager::getConfigurationStatic();

        try {
            $accessService = $this->getAccessService();
            $request = $accessService->getRequest($verificationCode);

            if (!$accessService->isRequestSuccesful($request)) {
                throw new \RuntimeException(AccessService::RESULT_REQUEST_FORM_VALIDATE_ERROR, 1620315944);
            }
            $accessService->denyAccess($request);
            $this->sendAccessDeniedEmailToSource($request, $accessConfiguration, $denyAccessFeedbackForm->getReason());
        } catch (\Exception $exception) {
            $this->handleException($exception);
        }
    }

    /**
     * Sets 'access is granted' e-mail to target user.
     */
    protected function sendRequestAccessEmail(
        Request $request,
        BpnRequestAccessConfiguration $allowAccessConfiguration
    ) {
        /** @var ExpiringGroupsService $expiringGroupsService */
        $expiringGroupsService = GeneralUtility::makeInstance(ObjectManager::class)
            ->get(ExpiringGroupsService::class);
        $activeExpiringGroups = $expiringGroupsService->getActiveExpiringGroups($request->getUserRequestTarget());

        try {
            $subject = TextService::getTextByLabel(
                'request_access_email_subject'
            );

            $emailAccessRequestView = $this->createViewClone('emailAccessRequest');
            $emailAccessRequestView->assign(
                'landingPage',
                $allowAccessConfiguration->getLandingPage()
            );
            $emailAccessRequestView->assign('request', $request);
            $emailAccessRequestView->assign('startTime', $request->getStart());
            $emailAccessRequestView->assign('endTime', $request->getEnd());
            $emailAccessRequestView->assign('activeExpiringGroups', $activeExpiringGroups);
            $output = $emailAccessRequestView->render();

            $this->emailService->send(
                [$allowAccessConfiguration->getExaminationAdminEmailAddress()],
                $subject,
                $output,
                $allowAccessConfiguration->getExaminationAdminName(),
                $allowAccessConfiguration->getExaminationAdminEmailAddress()
            );
        } catch (\Exception $exception) {
            $this->handleException($exception);
        }
    }

    /**
     * Sets 'access is granted' e-mail to target user.
     */
    protected function sendAccessGrantedEmailToTarget(
        Request $request,
        BpnRequestAccessConfiguration $allowAccessConfiguration
    ) {
        $requestTargetGrantedView = $this->createViewClone('emailAccessRequestTargetGranted');
        $requestTargetGrantedView->assign('request', $request);
        $requestTargetGrantedView->assign('startDate', $request->getStart());
        $requestTargetGrantedView->assign('endDate', $request->getEnd());
        $output = $requestTargetGrantedView->render();
        $this->emailService->send(
            [$this->getEmailFromUserRequestSource($request->getUserRequestTarget())],
            TextService::getTextByLabel(
                'request_access_granted_subject'
            ),
            $output,
            $allowAccessConfiguration->getExaminationAdminName(),
            $allowAccessConfiguration->getExaminationAdminEmailAddress()
        );
    }

    /**
     * Sets 'access is granted' e-mail to source user.
     */
    protected function sendAccessGrantedEmailToSource(
        Request $request,
        BpnRequestAccessConfiguration $allowAccessConfiguration
    ) {
        $requestSourceGrantedView = $this->createViewClone('emailAccessRequestSourceGranted');
        $requestSourceGrantedView->assign('request', $request);
        $requestSourceGrantedView->assign('startDate', $request->getStart());
        $requestSourceGrantedView->assign('endDate', $request->getEnd());
        $output = $requestSourceGrantedView->render();
        $this->emailService->send(
            [$this->getEmailFromUserRequestSource($request->getUserRequestSource())],
            TextService::getTextByLabel(
                'request_access_granted_subject'
            ),
            $output,
            $allowAccessConfiguration->getExaminationAdminName(),
            $allowAccessConfiguration->getExaminationAdminEmailAddress()
        );
    }

    /**
     * Sets 'access is denied' e-mail to source user.
     *
     * @param string $reason the denied reason
     */
    protected function sendAccessDeniedEmailToSource(
        Request $request,
        BpnRequestAccessConfiguration $allowAccessConfiguration,
        $reason
    ) {
        $requestDeniedView = $this->createViewClone('emailAccessRequestSourceDenied');
        $requestDeniedView->assign('request', $request);
        $requestDeniedView->assign('userRequestDeniedReason', $reason);
        $requestDeniedView->assign('startDate', $request->getStart());
        $requestDeniedView->assign('endDate', $request->getEnd());
        $output = $requestDeniedView->render();

        $this->emailService->send(
            [$this->getEmailFromUserRequestSource($request->getUserRequestSource())],
            TextService::getTextByLabel(
                'request_access_denied_source_subject'
            ),
            $output,
            $allowAccessConfiguration->getExaminationAdminName(),
            $allowAccessConfiguration->getExaminationAdminEmailAddress()
        );
    }

    /**
     * Shows message that users' account is not valid and should contact helpdesk.
     */
    public function accountNotValidAction()
    {
    }

    /**
     * Shows message that the request was not valid
     * 628160: means the submitted form could not be validated
     * 790051: means the verification code could not be validated and the request has been denied therefore
     * 785325: means the request was already processed
     * 448576: means the request could not be approved, reason unknown
     * 359771: means the email address is invalid.
     *
     * @param int $code
     */
    public function invalidRequestAction($code)
    {
        $message = $this->getAccessService()->getErrorMessage($code) ?? 'An exception has occurred';
        $this->view->assign('errors', [new \Exception($message, $code)]);
    }

    /**
     * Ensures the user has the proper permissions to request the access
     * NOTE: will redirect / forward to the access denied handler if user has no access!
     */
    protected function ensureAuthorized()
    {
        if ($this->authorizationService->isLoggedin()) {
            return;
        }
        $this->notAllowedAction(1539001483);
    }

    /**
     * Gets email from request user from username field of email field (fallback). Forwards to invalid request if no
     * valid email found.
     *
     * @return string
     *
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
     */
    private function getEmailFromUserRequestSource(FrontendUser $requestSource)
    {
        $email = $requestSource->getUsername();
        if (!GeneralUtility::validEmail($email)) {
            // fallback to email address in user profile
            $email = $requestSource->getEmail();
        }
        if (!GeneralUtility::validEmail($email)) {
            $this->forward(
                'invalidRequest',
                null,
                null,
                ['code' => \BPN\BpnRequestAccess\Service\AccessService::RESULT_REQUEST_INVALID_EMAIL]
            );
        }

        return $email;
    }

    public function injectAccessConfiguration(BpnRequestAccessConfiguration $accessConfiguration)
    {
        $this->accessConfiguration = $accessConfiguration;
    }

    public function injectAuthorizationService(AuthorizationService $authorizationService)
    {
        $this->authorizationService = $authorizationService;
    }

    public function injectEmailService(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }

    public function injectFrontendUserGroupRepository(FrontendUserGroupRepository $frontendUserGroupRepository)
    {
        $this->frontendUserGroupRepository = $frontendUserGroupRepository;
    }

    public function injectPageRepository(PageRepository $pageRepository)
    {
        $this->pageRepository = $pageRepository;
    }

    protected function getAccessService() : AccessService
    {
        return GeneralUtility::makeInstance(ObjectManager::class)
            ->get(AccessService::class);
    }

    protected function getConfiguration() : BpnRequestAccessConfiguration
    {
        return ExtensionConfigurationManager::getConfigurationStatic();
    }

    private function getAuthorizationService() : AuthorizationService
    {
        /* @var AuthorizationService $authorizationService */
        return GeneralUtility::makeInstance(ObjectManager::class)
            ->get(AuthorizationService::class);
    }

    /**
     * Action showing error page when not allowed to view
     * Note: should also define this in your ext_localconf.php for your extension.
     *
     * @param string $errorCode
     */
    public function notAllowedAction($errorCode = '')
    {
        $this->redirectToUri('/403/' . $errorCode ? '#' . $errorCode : '');
    }

    /**
     * Creates a clone of the current view and sets the new action.
     *
     * @param string $viewActionName the name of the action to render
     *
     * @return \TYPO3\CMS\Fluid\View\TemplateView
     */
    protected function createViewClone(string $viewActionName)
    {
        /** @var \TYPO3\CMS\Extbase\Mvc\View\ViewInterface $newView */
        $newView = $this->objectManager->get($this->defaultViewObjectName);
        $this->setViewConfiguration($newView);
        $requestClone = clone $this->request;
        $contextClone = clone $this->controllerContext;
        $requestClone->setControllerActionName($viewActionName);
        $contextClone->setRequest($requestClone);
        $newView->setControllerContext($contextClone);
        $newView->initializeView();

        /* @noinspection PhpIncompatibleReturnTypeInspection */
        return $newView;
    }

    private function toAssocArray(array $findByIdentifiers, string $valueField = '', string $keyField = '')
    {
        $result = [];
        foreach ($findByIdentifiers as $key => $item) {
            $value = is_array($item) ? $item[$valueField] : $item;
            $key = $keyField ? $item[$keyField] : $key;
            $result[$key] = $value;
        }

        return $result;
    }

    /**
     * @param \Exception $exception
     *
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
     */
    protected function handleException(\Exception $exception) : void
    {
        if (is_numeric($exception->getCode())) {
            $this->forward('invalidRequest', null, null, ['code' => (int)$exception->getMessage()]);
        } else {
            $this->view->assign('errors', [$exception]);
        }
    }
}
