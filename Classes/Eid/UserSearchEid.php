<?php

namespace  BpnRequestAccess\Eid;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

//todo

class UserSearchEid
{
    /** @var \TYPO3\CMS\Extbase\Domain\Model\FrontendUser $feUser */
    protected $feUser;

    public function start()
    {
        // Basic TSFE Setup - get all the Page data you may need.
        /** @var TypoScriptFrontendController $tsfe */
        $tsfe = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
            TypoScriptFrontendController::class,
            $GLOBALS['TYPO3_CONF_VARS'],
            1,
            0
        );

        $GLOBALS['TSFE']= $tsfe;
        $GLOBALS['TSFE']->initFEuser();
        $this->feUser = $GLOBALS['TSFE']->fe_user;

        if (isset($this->feUser->user['uid'])) {
            /** @var \TYPO3\CMS\Core\Database\DatabaseConnection $db */
            $db = $GLOBALS['TYPO3_DB'];

            $results = [];

            if (!empty($_GET['q'])) {
                $searchTerm = mysqli_real_escape_string($db->getDatabaseHandle(), $_GET['q']);

                $role = $this->determineRole();

                // TODO: Company field may be empty, need actual school of the user in a join?
                $query = "SELECT uid,name,company,username
FROM fe_users
WHERE disable = 0
AND deleted = 0
AND starttime <= UNIX_TIMESTAMP() AND (endtime = 0 OR endtime > UNIX_TIMESTAMP())
AND (name LIKE '%$searchTerm%' OR first_name LIKE '%$searchTerm%' OR last_name LIKE '%$searchTerm%' OR username LIKE '%$searchTerm%' OR email LIKE '%$searchTerm%')
$permWhere
ORDER BY name,company
LIMIT 50";

                $res = $db->sql_query($query);

                $numResults = $db->sql_num_rows($res);
                if ($numResults > 0) {
                    $results['numResults'] = $numResults;
                    while ($row = $db->sql_fetch_assoc($res)) {
                        $results['items'][] = [
                            'id' => $row['uid'],
                            'text' => $row['name'] . ' - ' . $row['company'] . ' - ' . $row['username'],
                        ];
                    }
                } else {
                    $results['numResults'] = 0;
                    $results['items'][] = [];   // add empty row to prevent JS error
                }
            } else {
                $results['numResults'] = 0;
                $results['items'][] = [];   // add empty row to prevent JS error
            }

            header('Content-type: application/json');
            echo json_encode($results);
        } else {
            http_response_code(401);
            die();
        }
    }

    private function determineRole()
    {
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $frontendUserGroupRepository = $objectManager->get(FrontendUserGroupRepository::class);
        //$leerlingGroupUids = $frontendUserGroupRepository->getIdsByHandle(UsergroupHandle::USERGROUP_LEERLING_HANDLE);
        $docentGroupUids = $frontendUserGroupRepository->getIdsByHandle(UsergroupHandle::USERGROUP_DOCENT_HANDLE);
        $docentExfuncGroupUids = $frontendUserGroupRepository->getIdsByHandle(UsergroupHandle::USERGROUP_DOCENT_EXFUNC_HANDLE);
        $exfuncGroupUids = $frontendUserGroupRepository->getIdsByHandle(UsergroupHandle::USERGROUP_EXFUNC_HANDLE);
        $splSchool = $frontendUserGroupRepository->getIdsByHandle(UsergroupHandle::USERGROUP_SPL_SCHOOL);

        // Check role
        if ($this->feUser->user['currentSchoolUid'] === (int)$splSchool[0]) {
            $role = 'medewerker';
        } elseif (ArrayFunctions::searchArrayMultiple($this->feUser->user['currentGroups'], 'uid', array_merge($docentGroupUids, $docentExfuncGroupUids, $exfuncGroupUids)) !== null) {
            $role = 'docent';
        } else {
            $role = 'student';
        }

        return $role;
    }
}

