<?php

return [
    'navigation' => [
        'title' => 'Programu-jalizi',
    ],

    'tabs' => [
        'apps'          => 'Apps',
        'extra'         => 'Extra',
        'installed'     => 'Installed',
        'not-installed' => 'Si Installed',
    ],

    'header-actions' => [
        'sync' => [
            'label'                     => 'Sync Inapatikana Programu-jalizi',
            'modal-heading'             => 'Sync Programu-jalizi',
            'modal-description'         => 'Hii ita scan na jisajili yoyote mpya programu-jalizi imepatikana.',
            'modal-submit-action-label' => 'Sync Programu-jalizi',

            'notification' => [
                'success' => [
                    'title' => 'Programu-jalizi Synced Successfully',
                    'body'  => 'Imepatikana na synced :count mpya programu-jalizi(s).',
                ],

                'error' => [
                    'title' => 'Programu-jalizi Sync Imeshindwa',
                    'body'  => 'hitilafu (:error) occurred wakati syncing programu-jalizi. Tafadhali jaribu tena.',
                ],
            ],
        ],
    ],
];
