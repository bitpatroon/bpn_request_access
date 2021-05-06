<?php

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2021 Sjoerd Zonneveld  <code@bitpatroon.nl>
 *  Date: 29-4-2021 13:27
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
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

namespace BPN\BpnRequestAccess\Service;

use Symfony\Component\Mime\Address;
use TYPO3\CMS\Core\Mail\MailMessage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

class EmailService
{
    /**
     * @var bool
     */
    protected $sendMail = true;

    public function disableSendMail(bool $disable)
    {
        $this->sendMail = !$disable;

        return $this;
    }

    /**
     * Sends the given mail to the given recipients.
     *
     * @param string[]    $recipients  the recipients
     * @param string      $subject     the subject
     * @param string      $html        the message
     * @param string      $fromName    the from name
     * @param string      $fromAddress the from address
     * @param bool|string $returnPath
     *
     * @return bool|string true or the recipients that failed
     */
    public function send(
        array $recipients,
        string $subject,
        string $html,
        string $fromName,
        string $fromAddress,
        string $returnPath = '',
        string $replyTo = ''
    ) : bool {
        $result = true;

        if ($this->sendMail) {
            /** @var MailMessage $mail */
            $mail = GeneralUtility::makeInstance(ObjectManager::class)
                ->get(MailMessage::class);

            $to = [];
            foreach ($recipients as $recipient) {
                $to[] = new Address($recipient);
            }

            $mail
                ->from(new Address($fromAddress, $fromName))
                ->setTo($to)
                ->subject(htmlspecialchars($subject))
                ->html($html)
                ->text(strip_tags($html));

            if ($returnPath) {
                $mail->returnPath(new Address($returnPath));
            }
            if ($replyTo) {
                $mail->replyTo(new Address($replyTo));
            }

            return $mail->send();
        }

        return $result;
    }

}
