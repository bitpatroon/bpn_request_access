<?php

/* * *************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Frans van der Veen
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

namespace BPN\BpnRequestAccess\Domain\DataProvider;

use BPN\BpnRequestAccess\Domain\Repository\FrontendUserGroupRepository;
use BPN\BpnRequestAccess\Domain\Repository\FrontendUserRepository;
use BPN\BpnRequestAccess\Domain\Repository\PageRepository;
use Countable;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Domain\Model\FrontendUserGroup;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings;

class AccessFormDataProvider
{
    /**
     * @var FrontendUserGroupRepository
     *
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
     * Gets all opleidingsgebieden for a school
     *
     * @param object|int $school id or school object
     *
     * @return array
     */
    public function getOpleidingsgebiedenForSchool($school)
    {
        $result = [];
        if (is_numeric($school)) {
            /** @var FrontendUserGroup $school */
            $school = $this->frontendUserGroupRepository->findByUid((int)$school);
        }
        if ($school !== null && is_object($school) && count($school->getOpleidingsgebieden()) > 0) {
            foreach ($school->getOpleidingsgebieden() as $subgroup) {
                /** @var FrontendUserGroup $subgroup */
                $result[$subgroup->getDescription()] = $subgroup;
            }
            ksort($result);
        }

        return $result;
    }

    /**
     * Gets all opleidingsgebieden for a school for examination
     *
     * @param object|int $school id or school object
     *
     * @return array
     */
    public function getOpleidingsgebiedenForExaminationForSchool($school)
    {
        $result = [];
        // resolve handle to pid
//        $storagePid = $this->pageRepository->getIdsByHandle('PAGE_HANDLE_QUALIFICATION_EXAMINATION');

        if (is_numeric($school)) {
            /** @var FrontendUserGroup $school */
            $school = $this->frontendUserGroupRepository->findByUid((int)$school);
        }
        if ($school !== null && is_object($school) && count($school->getOpleidingsgebiedenExaminering()) > 0) {
            foreach ($school->getOpleidingsgebiedenExaminering() as $subgroup) {
                $result[$subgroup->getDescription()] = $subgroup;
            }
            ksort($result);
        }

        return $result;
    }

    /**
     * Gets the teachers assigned for this school
     *
     * @param FrontendUserGroup $school
     *
     * @return array
     */
    public function getTeachersForSchool(FrontendUserGroup $school)
    {
        /** @var PageRepository $pageRepository */
        $pageRepository = GeneralUtility::makeInstance(ObjectManager::class)
            ->get(PageRepository::class);

        $teacherStoragePageIds = $pageRepository->getIdsByHandle('PAGE_HANDLE_FRONTEND_USERS');
        $testStoragePageIds = $pageRepository->getIdsByHandle('FRONTEND_USERS_TEST_STORAGE_HANDLE');
        $storagePageIds = array_merge($teacherStoragePageIds, $testStoragePageIds);
        $defaultSettings = $this->frontendUserRepository->getDefaultQuerySettings();
        $querySettings = new Typo3QuerySettings();
        $querySettings->setStoragePageIds($storagePageIds);
        $this->frontendUserRepository->setDefaultQuerySettings($querySettings);
        $result = $this->frontendUserRepository->findByUsergroup($school);
        if ($defaultSettings !== null) {
            $this->frontendUserRepository->setDefaultQuerySettings($defaultSettings);
        }

        return $this->formatTeacherResultForSelect($result);
    }

    public function injectFrontendUserGroupRepository(FrontendUserGroupRepository $frontendUserGroupRepository
    ) {
        $this->frontendUserGroupRepository = $frontendUserGroupRepository;
    }

    public function injectFrontendUserRepository(
        FrontendUserRepository $frontendUserRepository
    ) {
        $this->frontendUserRepository = $frontendUserRepository;
    }

    public function injectPageRepository(PageRepository $pageRepository)
    {
        $this->pageRepository = $pageRepository;
    }

    /**
     * Formats given teacher resultset to a nicely representable result
     *
     * @param array|Countable $teachers
     *
     * @return array
     */
    protected function formatTeacherResultForSelect($teachers)
    {
        $result = [];
        if (count($teachers) == 0) {
            return [];
        }
        /** @var \TYPO3\CMS\Extbase\Domain\Model\FrontendUser $item */
        foreach ($teachers as $item) {
            $result[] = [
                'name' => sprintf(
                    '%1$s %2$s %3$s (%4$s)',
                    ucwords($item->getFirstName()),
                    $item->getMiddleName(),
                    ucwords($item->getLastName()),
                    $item->getUsername()
                ),
                'uid'  => $item->getUid()
            ];
        }

        return $result;
    }
}
