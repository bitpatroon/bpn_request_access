<?php

use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

defined('TYPO3_MODE') or die('¯\_(ツ)_/¯');

ExtensionUtility::registerPlugin('bpn_request_access', 'requestaccess', 'BPN Request Access');

$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist']['bpnrequestaccess_requestaccess'] = 'layout,select_key,recursive';
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['bpnrequestaccess_requestaccess'] = 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
    'bpnrequestaccess_requestaccess',
    'FILE:EXT:bpn_request_access/Configuration/FlexForm/flexform.xml'
);
