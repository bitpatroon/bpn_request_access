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

namespace BPN\BpnRequestAccess\Service;

use TYPO3\CMS\Core\Utility\GeneralUtility;

class VerificationCodeService
{
    const SEPARATION_CODE = 's4w9ql';
    const MAX_DEPTH = 15;

    /**
     * @var int
     */
    private $depth = 0;

    /**
     * Creates verification code with the given input
     *
     * @param string $input                           the string to encode
     * @param int    $numberOfSecondsBeforeExpiration the number of seconds before the hash becomes invalid
     * @param string $secureKey                       key with which the salt will be generated
     *
     * @return string
     */
    public function createVerificationCode(string $input, int $numberOfSecondsBeforeExpiration, string $secureKey)
    {
        $expirationTime = time() + $this->depth + $numberOfSecondsBeforeExpiration;
        $randomHash = hash_hmac(
            'sha256',
            $input . $expirationTime,
            $secureKey
        );

        if (strpos($randomHash, self::SEPARATION_CODE) !== false) {
            if ($this->depth < self::MAX_DEPTH) {
                $this->depth++;
                $result = $this->createVerificationCode($input, $numberOfSecondsBeforeExpiration, $secureKey);
            } else {
                $result = $expirationTime . ':' . $randomHash;
            }
        } else {
            $result = $expirationTime . self::SEPARATION_CODE . $randomHash;
        }

        return $result;
    }

    /**
     * Checks if the hash is valid
     *
     * @param string $hash                            the hash supplied
     * @param string $input                           data to use to create hash
     * @param int    $numberOfSecondsBeforeExpiration the number of seconds before the hash becomes invalid
     * @param string $secureKey                       secure key that only we know
     *
     * @return bool
     */
    public function isValid($hash, $input, $numberOfSecondsBeforeExpiration, $secureKey)
    {
        $parts = [];
        $result = false;

        if (strpos($hash, self::SEPARATION_CODE) !== false) {
            $parts = GeneralUtility::trimExplode(self::SEPARATION_CODE, $hash);
        } elseif (strpos($hash, ':') !== false) {
            $parts = GeneralUtility::trimExplode(':', $hash);
        }

        if (count($parts) == 2) {
            $timeHashWasCreated = $parts[0];
            $timeExpiredSinceCreation = time() - ($timeHashWasCreated - $numberOfSecondsBeforeExpiration);
            $randomHash = $parts[1];
            $checkHash = hash_hmac(
                'sha256',
                $input . $timeHashWasCreated,
                $secureKey
            );

            $result = $randomHash === $checkHash && ($timeExpiredSinceCreation < $numberOfSecondsBeforeExpiration);
        }

        return $result;
    }
}
