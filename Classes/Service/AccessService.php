<?php

/* * *************************************************************
 *  Copyright notice
 *
 *  (c) 2016 Frans van der Veen
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
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
 * ************************************************************* */

namespace BPN\BpnRequestAccess\Service;

use BPN\BpnRequestAccess\Domain\Form\AccessForm;
use BPN\BpnRequestAccess\Domain\Model\Request;
use BPN\BpnRequestAccess\Domain\Repository\FrontendUserGroupRepository;
use BPN\BpnRequestAccess\Domain\Repository\FrontendUserRepository;
use BPN\BpnRequestAccess\Domain\Repository\PageRepository;
use BPN\BpnRequestAccess\Domain\Repository\RequestRepository;
use BPN\Configuration\Configuration\ExtensionConfigurationManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Domain\Model\FrontendUser;
use TYPO3\CMS\Extbase\Domain\Model\FrontendUserGroup;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings;

/**
 * Class \BpnRequestAccess\Service\AccessService.
 */
class AccessService
{
    const RESULT_REQUEST_ALREADY_PROCESSED = 785325;
    const RESULT_REQUEST_NOT_FOUND = 790051;
    const RESULT_REQUEST_FORM_VALIDATE_ERROR = 628160;
    const RESULT_REQUEST_COULD_NOT_APPROVE = 448576;
    const RESULT_REQUEST_INVALID_EMAIL = 359771;

    public const PAGE_HANDLE_REQUEST_ACCESS = 'page_handle_request_access';

    /**
     * @var FrontendUserGroupRepository
     */
    protected $frontendUserGroupRepository;

    /**
     * @var FrontendUserRepository
     */
    protected $frontendUserRepository;

    /**
     * @var PageRepository
     */
    protected $pageRepository;

    /**
     * @var RequestRepository
     */
    protected $requestRepository;

    public function initializeObject()
    {
        /* @var PageRepository $pageRepository */
        $this->pageRepository = GeneralUtility::makeInstance(ObjectManager::class)
            ->get(PageRepository::class);

        $querySettings = new Typo3QuerySettings();
        $querySettings->setStoragePageIds([$this->getStorageFolderId()]);
        $this->requestRepository->setDefaultQuerySettings($querySettings);
    }

    public function injectFrontendUserGroupRepository(FrontendUserGroupRepository $frontendUserGroupRepository)
    {
        $this->frontendUserGroupRepository = $frontendUserGroupRepository;
    }

    public function injectFrontendUserRepository(FrontendUserRepository $frontendUserRepository)
    {
        $this->frontendUserRepository = $frontendUserRepository;
    }

    public function injectRequestRepository(RequestRepository $requestRepository)
    {
        $this->requestRepository = $requestRepository;
    }

    /**
     *    Checks if the access request being done is valid.
     */
    public function validateAccessRequestData(AccessForm $accessForm) : bool
    {
        $targetTime = strtotime($accessForm->getPermittedDuration());

        return $targetTime && ($targetTime > time());
    }

    /**
     * Creates access request and returns it.
     *
     * @param \DateTime $start
     * @param string    $duration
     *
     * @return Request
     *
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     */
    public function createAccessRequest(
        FrontendUser $userRequestTarget,
        FrontendUser $userRequestSource,
        FrontendUserGroup $usergroup,
        $start,
        $duration
    ) {
        $storagePid = $this->getStorageFolderId();

        /** @var VerificationCodeService $verificationCodeService */
        $verificationCodeService = GeneralUtility::makeInstance(VerificationCodeService::class);
        $request = new Request();
        $title = sprintf(
            '"%1$s" access to "%2$s" for "%3$s" starting at "%4$s" by %5$s',
            $userRequestTarget->getUsername(),
            $usergroup->getTitle(),
            $duration,
            $start->format('d-m-Y'),
            $userRequestSource->getUsername()
        );
        $request
            ->setUserRequestTarget($userRequestTarget)
            ->setUserRequestSource($userRequestSource)
            ->setTitle($title)
            ->setStart($start)
            ->setDuration($duration)
            ->setUsergroup($usergroup)
            ->setPid($storagePid);
        $data['username'] = $userRequestTarget->getUsername();
        $data['opleidingsgebied'] = $usergroup->getUid();
        $data['start'] = $start->format('d-m-Y');
        $data['duration'] = $duration;
        $allowAccessConfiguration = ExtensionConfigurationManager::getConfigurationStatic();
        $request->setVerificationCode(
            $verificationCodeService->createVerificationCode(
                strtolower(implode($data)),
                $allowAccessConfiguration->getVerificationCodeNumberOfSecondsBeforeExpiration(),
                $allowAccessConfiguration->getVerificationCodeSecureKey()
            )
        );
        $this->requestRepository->add($request);

        return $request;
    }

    /**
     * Allows access request.
     *
     * @param string $verificationCode supplied verification code
     * @param Request $request
     *
     * @return bool true if successful false othwerise
     */
    public function grantAccess($verificationCode, $request)
    {
        if (!$request instanceof Request) {
            return false;
        }
        $data['username'] = $request->getUserRequestTarget()->getUsername();
        $data['usergroup'] = $request->getUsergroup()->getUid();
        $data['start'] = $request->getStart()->format('d-m-Y');
        $data['duration'] = $request->getDuration();
        $data = strtolower(implode($data));
        if ($this->isValidRequest($verificationCode, $data)) {
            // actually permit access here
            $request->setRequestResult(Request::RESULT_ALLOWED);

            /** @var FrontendUserRepository $frontendUserRepository */
            $frontendUserRepository = GeneralUtility::makeInstance(ObjectManager::class)
                ->get(FrontendUserRepository::class);

            // add permission to user (usergroup)
            $frontendUserRepository->addExpiringGroup(
                $request->getUserRequestTarget(),
                $request->getUsergroup()->getUid(),
                $request->getStart()->getTimestamp(),
                $request->getEnd()->getTimestamp()
            );

            $result = true;
        } else {
            $request->setRequestResult(Request::RESULT_DENIED);
            $result = false;
        }
        $this->requestRepository->update($request);

        return $result;
    }

    /**
     * Denies access.
     *
     * @param Request $request
     *
     * @return bool
     *
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException
     */
    public function denyAccess($request)
    {
        if (!$request instanceof Request) {
            return false;
        }
        $request->setRequestResult(Request::RESULT_DENIED);
        $this->requestRepository->update($request);

        return true;
    }

    /**
     * Gets the request thorugh the given verificationCode, or error result code.
     *
     * @param string $verificationCode
     *
     * @return Request|int
     */
    public function getRequest($verificationCode)
    {
        /** @var Request $result */
        $result = $this->requestRepository->findOneByVerificationCode($verificationCode);
        if (null === $result) {
            return self::RESULT_REQUEST_NOT_FOUND;
        }
        if (Request::RESULT_UNVOTED != $result->getRequestResult()) {
            return self::RESULT_REQUEST_ALREADY_PROCESSED;
        }

        return $result;
    }

    /**
     * Checks if the given request was retrieved successfully.
     *
     * @param mixed $request
     *
     * @return bool
     */
    public function isRequestSuccesful($request)
    {
        return null !== $request && !is_int($request) && $request instanceof Request;
    }

    private function isValidRequest(string $verificationCode, string $data)
    {
        /** @var VerificationCodeService $verificationCodeService */
        $verificationCodeService = GeneralUtility::makeInstance(ObjectManager::class)
            ->get(VerificationCodeService::class);

        $allowAccessConfiguration = ExtensionConfigurationManager::getConfigurationStatic();

        return $verificationCodeService->isValid(
            $verificationCode,
            $data,
            $allowAccessConfiguration->getVerificationCodeNumberOfSecondsBeforeExpiration(),
            $allowAccessConfiguration->getVerificationCodeSecureKey()
        );
    }

    protected function getStorageFolderId() : int
    {
        $storageFolderId = $this->pageRepository->getIdsByHandle(self::PAGE_HANDLE_REQUEST_ACCESS);

        if ($storageFolderId) {
            return $storageFolderId;
        }

        throw new \RuntimeException(
            sprintf(
                "Page with handle not found. Please create a page in the backend and add a page handle with name '%s' to it.",
                self::PAGE_HANDLE_REQUEST_ACCESS
            ), 1620286701
        );
    }

    /**
     * @param int $code
     */
    public function getErrorMessage($code)
    {
        switch ((int)$code) {
            case self::RESULT_REQUEST_ALREADY_PROCESSED:
                return 'already-processed';
            case self::RESULT_REQUEST_NOT_FOUND:
                return 'request-not-found';
            case self::RESULT_REQUEST_FORM_VALIDATE_ERROR:
                return 'form-validation-error';
            case self::RESULT_REQUEST_COULD_NOT_APPROVE:
                return 'failed-to-approve';

            case self::RESULT_REQUEST_INVALID_EMAIL:
                return 'invalid-email';
            default:
                return null;
        }
    }
}
