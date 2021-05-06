<?php

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2021 Sjoerd Zonneveld  <code@bitpatroon.nl>
 *  Date: 29-4-2021 17:30
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

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Domain\Model\FrontendUser;
use TYPO3\CMS\Extbase\Domain\Repository\FrontendUserGroupRepository;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings;

class ExpiringGroupsService
{
    protected const RE_GROUP_UIDS = '/(\d+)\|(\d+)\|(\d+)\**/';

    /**
     * Gets all active expiring groups formatted with array('group', 'startDate', 'endDate').
     *
     * @return array
     */
    public function getActiveExpiringGroups(FrontendUser $frontendUser) : array
    {
        if (!class_exists('BPN\ExpiringFeGroups\Domain\Repository\FrontEndUserRepository')) {
            return [];
        }

        /** @var AuthorizationService $authorizationService */
        $authorizationService = GeneralUtility::makeInstance(ObjectManager::class)
            ->get(AuthorizationService::class);
        $expiringGroups = $authorizationService->getUserField('tx_expiringfegroups_groups');
        if (!$expiringGroups) {
            return [];
        }

        /** @var FrontendUserGroupRepository $frontendUserGroupRepository */
        $frontendUserGroupRepository = GeneralUtility::makeInstance(ObjectManager::class)
            ->get(FrontendUserGroupRepository::class);

        /** @var \TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings $querySettings */
        $querySettings = GeneralUtility::makeInstance(Typo3QuerySettings::class);
        $querySettings->setRespectStoragePage(false);
        $frontendUserGroupRepository->setDefaultQuerySettings($querySettings);

        $count = preg_match_all(self::RE_GROUP_UIDS, $expiringGroups, $matches);
        if (0 == $count) {
            return [];
        }

        $now = time();

        $result = [];
        [, $uids, $startTimes, $endTimes] = $matches;
        foreach ($uids as $index => $uid) {
            // only handle valid lines
            if (!isset($startTimes[$index], $endTimes[$index])) {
                continue;
            }
            if (empty($uid)) {
                continue;
            }
            $start = (int)$startTimes[$index];
            $end = (int)$endTimes[$index];

            // only register active uids
            if ($now <= $start || $now >= $end) {
                continue;
            }

            $row = [];
            $group = $frontendUserGroupRepository->findByUid((int)$uid);
            if (null === $group) {
                continue;
            }
            $row['group'] = $group;
            $row['startDate'] = new \DateTime('@' . $start);
            $row['endDate'] = new \DateTime('@' . $end);
            $result[] = $row;
        }

        return $result;
    }

}
