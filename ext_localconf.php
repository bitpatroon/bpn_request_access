<?php

if (!defined('TYPO3_MODE')) {
    exit('Access denied.');
}

call_user_func(
    function () {
// registration part
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
            'BpnRequestAccess',
            'RequestAccess',
            [
                \BPN\BpnRequestAccess\Controller\RequestAccessController::class =>
                    implode(
                        ',',
                        [
                            'requestAccessForm',
                            'index',
                            'requestAccess',
                            'grantAccess',
                            'denyAccess',
                            'denyAccessWithFeedback',
                            'accountNotValid',
                            'invalidRequest'
                        ]
                    )
            ],
            [
                \BPN\BpnRequestAccess\Controller\RequestAccessController::class =>
                    implode(
                        ',',
                        [
                            'requestAccessForm',
                            'index',
                            'requestAccess',
                            'grantAccess',
                            'denyAccess',
                            'denyAccessWithFeedback',
                            'accountNotValid',
                            'invalidRequest'
                        ]
                    )
            ]
        );

        $GLOBALS['TYPO3_CONF_VARS']['FE']['eID_include']['bpn_request_access_usersearch'] =
            \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath(
                'bpn_request_access'
            ) . 'Classes/Eid/UserSearchEid.php';

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptConstants(
            '<INCLUDE_TYPOSCRIPT: source="FILE:EXT:bpn_request_access/Configuration/TypoScript/constants.typoscript">'
        );
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptSetup(
            '<INCLUDE_TYPOSCRIPT: source="FILE:EXT:bpn_request_access/Configuration/TypoScript/setup.typoscript">'
        );
    }
);

