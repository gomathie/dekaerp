<?php

return [
    'navigation' => [
        'title' => 'Programu-jalizi',
    ],

    'tabs' => [
        'apps'          => 'Programu',
        'extra'         => 'Ziada',
        'installed'     => 'Zilizosakinishwa',
        'not-installed' => 'Hazijasakinishwa',
    ],

    'header-actions' => [
        'sync' => [
            'label'                     => 'Sawazisha programu-jalizi',
            'modal-heading'             => 'Sawazisha programu-jalizi',
            'modal-description'         => 'Hii itatafuta na kusajili programu-jalizi mpya zozote zitakazopatikana.',
            'modal-submit-action-label' => 'Sawazisha',

            'notification' => [
                'success' => [
                    'title' => 'Programu-jalizi zimesawazishwa',
                    'body'  => 'Programu-jalizi mpya :count zimepatikana na kusawazishwa.',
                ],

                'error' => [
                    'title' => 'Usawazishaji umeshindwa',
                    'body'  => 'Hitilafu (:error) imetokea wakati wa kusawazisha programu-jalizi. Tafadhali jaribu tena.',
                ],
            ],
        ],
    ],
];
