<?php

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2021 Sjoerd Zonneveld  <code@bitpatroon.nl>
 *  Date: 29-4-2021 17:59
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

namespace BPN\BpnRequestAccess\Domain\Repository;

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Domain\Model\FrontendUser;

class FrontendUserRepository extends \TYPO3\CMS\Extbase\Domain\Repository\FrontendUserRepository
{
    /**
     * Adds expiring group to list
     *
     * @param int $groupUid
     * @param int $start
     * @param int $end
     */
    public function addExpiringGroup(FrontendUser $user, $groupUid, $start, $end)
    {
        $uid = $user->getUid();
        if (!$uid) {
            return;
        }

        /** Connection $connection */
        $connection = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionForTable('fe_users');

        $row = $connection->select(['tx_expiringfegroups_groups'], 'fe_users', ['uid' => $uid])->fetchAssociative();

        $expiringGroups = explode('*', $row['tx_expiringfegroups_groups'] ?? '');
        $expiringGroups[] = implode('|', [$groupUid, $start, $end]);

        $connection->update(
            'fe_users',
            ['tx_expiringfegroups_groups' => implode('*', $expiringGroups)],
            ['uid' => $uid]
        );
    }
}
