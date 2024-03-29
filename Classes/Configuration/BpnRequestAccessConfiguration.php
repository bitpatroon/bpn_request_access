<?php

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2021 Sjoerd Zonneveld  <code@bitpatroon.nl>
 *  Date: 6-5-2021 21:38
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

namespace BPN\BpnRequestAccess\Configuration;

use BPN\Configuration\Configuration\AbstractExtensionConfiguration;

class BpnRequestAccessConfiguration extends AbstractExtensionConfiguration
{
    /**
     * @var string
     */
    protected $pluginName = 'requestaccess';

    /**
     * @var string
     */
    protected $verificationCodeSecureKey;

    /**
     * @var int
     */
    protected $verificationCodeNumberOfSecondsBeforeExpiration;

    /**
     * @var array
     */
    protected $permittedDurations;

    /**
     * @var bool
     */
    protected $logEmail;

    /**
     * @var bool
     */
    protected $doNotSendMail;

    /** @var string */
    protected $serviceEmail;

    /**
     * @var string
     */
    protected $examinationAdminEmailAddress;

    /**
     * @var string
     */
    protected $examinationAdminName;

    /**
     * @var string
     */
    protected $landingPage;

    /**
     * Initializes the application configuration.
     *
     * @param array $settings
     */
    protected function initializeApplication($settings)
    {
        $durations = [];
        if ($settings['permitDurations'] && is_array($settings['permitDurations'])) {
            foreach ($settings['permitDurations'] as $key => $value) {
                if (empty($value['label'])) {
                    throw new \RuntimeException('The label for the given date is empty', 1619690660);
                }
                $dateValue = $value['value'];
                $date = $this->checkDate($dateValue);

                if ((int)$value['future'] && $value['valueAddendum']) {
                    if ($date < time()) {
                        $dateValue .= ' ' . $value['valueAddendum'];
                        $date = $this->checkDate($dateValue);
                    }
                }

                $durations[$value['value']] = $value['label'];
            }
        }
        $this->permittedDurations = $durations;
        $this->verificationCodeSecureKey = $this->getRequiredValueFromSettings(
            $settings,
            'verificationCode.secureKey',
            'Secure key for verification code generation should be set in your TypoScript template. (verificationCode.secureKey)',
            1619729031
        );
        $this->verificationCodeNumberOfSecondsBeforeExpiration = $this->getRequiredValueFromSettings(
            $settings,
            'verificationCode.numberOfSecondsBeforeExpiration',
            'Number of seconds before expiration should be set in your TypoScript template. (verificationCode.numberOfSecondsBeforeExpiration)',
            1619729032
        );
        $this->examinationAdminName = $this->getRequiredValueFromSettings(
            $settings,
            'email.validatorName',
            'Validator name was not configured and should be set in your TypoScript template. (email.validatorName)',
            1619729033
        );
        $this->examinationAdminEmailAddress = $this->getRequiredValueFromSettings(
            $settings,
            'email.validatorEmail',
            'Validator email was not configured and should be set in your TypoScript template. (email.validatorEmail)',
            1619729034
        );
        if (isset($settings['email']) && isset($settings['email']['serviceDeskEmail']) && $settings['email']['serviceDeskEmail']) {
            $this->serviceEmail = $settings['email']['serviceDeskEmail'];
        } else {
            $this->serviceEmail = $this->examinationAdminEmailAddress;
        }
        if ($settings['action'] === 'request') {
            $this->landingPage = (int)$this->getRequiredValueFromSettings(
                $settings,
                'landingPage',
                'Please configure the landing_page in the plugin configuration',
                1619729035
            );
        }
        $this->logEmail = (bool)$settings['emailDebug']['log'];
        $this->doNotSendMail = (bool)$settings['emailDebug']['doNotSendMail'];
    }

    /**
     * Gets the landingPage property.
     *
     * @return string
     */
    public function getLandingPage()
    {
        return $this->landingPage;
    }

    /**
     * Gets the examinationAdminName property.
     *
     * @return string
     */
    public function getExaminationAdminName()
    {
        return $this->examinationAdminName;
    }

    /**
     * Gets the examinationAdminEmailAddress property.
     *
     * @return string
     */
    public function getExaminationAdminEmailAddress()
    {
        return $this->examinationAdminEmailAddress;
    }

    /**
     * Gets the logEmail property.
     *
     * @return bool
     */
    public function getLogEmail()
    {
        return $this->logEmail;
    }

    /**
     * Gets the doNotSendMail property.
     *
     * @return bool
     */
    public function getDoNotSendMail()
    {
        return $this->doNotSendMail;
    }

    /**
     * Gets the permittedDurations property.
     *
     * @return array
     */
    public function getPermittedDurations()
    {
        return $this->permittedDurations;
    }

    /**
     * Gets the verificationCodeSecureKey property.
     *
     * @return string
     */
    public function getVerificationCodeSecureKey()
    {
        return $this->verificationCodeSecureKey;
    }

    /**
     * @return string
     */
    public function getServiceEmail() : string
    {
        return $this->serviceEmail;
    }


    /**
     * Gets the verificationCodeNumberOfSecondsBeforeExpiration property.
     *
     * @return int
     */
    public function getVerificationCodeNumberOfSecondsBeforeExpiration()
    {
        return $this->verificationCodeNumberOfSecondsBeforeExpiration;
    }

    protected function checkDate(string $dateValue) : int
    {
        $date = strtotime($dateValue);
        if (!$date) {
            throw new \RuntimeException(
                'The date offset specified in allowedDurations cannot be interpreted by php', 1619690664
            );
        }

        return $date;
    }
}
