<?php

return [
    'ctrl'      => [
        'title'          => 'LLL:EXT:bpn_request_access/Resources/Private/Language/locallang_backend.xlf:tx_bpnrequestaccess_domain_model_request',
        'label'          => 'title',
        'tstamp'         => 'tstamp',
        'crdate'         => 'crdate',
        'delete'         => 'deleted',
        'default_sortby' => 'ORDER BY title DESC',
        'enablecolumns'  => [
            'disabled' => 'hidden',
        ],
        'iconfile'       => 'EXT:bpn_request_access/ext_icon.png'
    ],
    'interface' => [
        'showRecordFieldList' => 'hidden, title'
    ],
    'columns'   => [
        'hidden'              => [
            'exclude' => 1,
            'label'   => 'LLL:EXT:lang/locallang_general.xlf:LGL.hidden',
            'config'  => [
                'type' => 'check'
            ]
        ],
        'title'               => [
            'exclude' => 0,
            'label'   => 'LLL:EXT:bpn_request_access/Resources/Private/Language/locallang_backend.xlf:tx_bpnrequestaccess_domain_model_request.title',
            'config'  => [
                'type' => 'input',
                'size' => 80,
                'eval' => 'trim,required',
            ]
        ],
        'verification_code'   => [
            'exclude' => 0,
            'label'   => 'LLL:EXT:bpn_request_access/Resources/Private/Language/locallang_backend.xlf:tx_bpnrequestaccess_domain_model_request.verification_code',
            'config'  => [
                'type' => 'input',
                'size' => 80,
                'eval' => 'trim,required',
            ]
        ],
        'start'               => [
            'exclude' => 0,
            'label'   => 'LLL:EXT:bpn_request_access/Resources/Private/Language/locallang_backend.xlf:tx_bpnrequestaccess_domain_model_request.start',
            'config'  => [
                'renderType' => 'inputDateTime',
                'type' => 'input',
                'size' => 20,
                'eval' => 'date,trim,required',
            ]
        ],
        'duration'            => [
            'exclude' => 0,
            'label'   => 'LLL:EXT:bpn_request_access/Resources/Private/Language/locallang_backend.xlf:tx_bpnrequestaccess_domain_model_request.duration',
            'config'  => [
                'type' => 'input',
                'size' => 80,
                'max'  => 40,
                'eval' => 'trim,required',
            ]
        ],
        'user_request_target' => [
            'exclude' => 1,
            'label'   => 'LLL:EXT:bpn_request_access/Resources/Private/Language/locallang_backend.xlf:tx_bpnrequestaccess_domain_model_request.user_request_target',
            'config'  => [
                'type'          => 'group',
                'internal_type' => 'db',
                'allowed'       => 'fe_users',
                'foreign_table' => 'fe_users',
                'size'          => 1,
                'minitems'      => 0,
                'maxitems'      => 1,
            ],
        ],
        'user_request_source' => [
            'exclude' => 1,
            'label'   => 'LLL:EXT:bpn_request_access/Resources/Private/Language/locallang_backend.xlf:tx_bpnrequestaccess_domain_model_request.user_request_source',
            'config'  => [
                'type'          => 'group',
                'internal_type' => 'db',
                'allowed'       => 'fe_users',
                'foreign_table' => 'fe_users',
                'size'          => 1,
                'minitems'      => 0,
                'maxitems'      => 1,
            ],
        ],
        'request_result'      => [
            'exclude' => 1,
            'label'   => 'LLL:EXT:bpn_request_access/Resources/Private/Language/locallang_backend.xlf:tx_bpnrequestaccess_domain_model_request.request_result',
            'config'  => [
                'type'       => 'select',
                'renderType' => 'selectSingle',
                'items'      => [
                    ['LLL:EXT:bpn_request_access/Resources/Private/Language/locallang_backend.xlf:tx_bpnrequestaccess_domain_model_request.request_result.I.0', 0],
                    ['LLL:EXT:bpn_request_access/Resources/Private/Language/locallang_backend.xlf:tx_bpnrequestaccess_domain_model_request.request_result.I.1', 1],
                    ['LLL:EXT:bpn_request_access/Resources/Private/Language/locallang_backend.xlf:tx_bpnrequestaccess_domain_model_request.request_result.I.2', 2],
                ],
            ],
        ],
        'usergroup'    => [
            'exclude' => 1,
            'label'   => 'LLL:EXT:bpn_request_access/Resources/Private/Language/locallang_backend.xlf:tx_bpnrequestaccess_domain_model_request.usergroup',
            'config'  => [
                'type'          => 'select',
                'renderType'    => 'selectSingle',
                'foreign_table' => 'fe_groups',
                'size'          => 1,
                'minitems'      => 0,
                'maxitems'      => 1,
            ],
        ],
    ],
    'types'     => [
        '1' => ['showitem' => 'hidden,title,verification_code,start,duration,user_request_target,user_request_source,usergroup,request_result']
    ],
    'palettes'  => [
        '1' => ['showitem' => '']
    ]
];
