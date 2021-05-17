<?php

defined('TYPO3_MODE') or exit('¯\_(ツ)_/¯');

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

$newFields = [
    'display_title' => [
        'label'  => 'LLL:EXT:bpn_request_access/Resources/Private/Language/locallang.xlf:display_title',
        'config' => [
            'type' => 'input',
        ],
    ],
];

// =======================================
//  Add all new TCA columns to fe_groups
// =======================================
ExtensionManagementUtility::addTCAcolumns(
    'fe_groups',
    $newFields
);

$newFieldNames = array_keys($newFields);
$newFieldNames = implode(',', $newFieldNames);

ExtensionManagementUtility::addToAllTCAtypes(
    'fe_groups',
    $newFieldNames
);
