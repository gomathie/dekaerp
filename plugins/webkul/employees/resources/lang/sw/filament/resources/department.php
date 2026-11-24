<?php

return [
    'title' => 'Idara',

    'navigation' => [
        'title' => 'Idara',
    ],

    'global-search' => [
        'department-manager' => 'Meneja',
        'company'            => 'Kampuni',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Taarifa za jumla',

                'fields' => [
                    'name'                => 'Jina',
                    'manager'             => 'Meneja',
                    'parent-department'   => 'Idara mama',
                    'manager-placeholder' => 'Chagua meneja',
                    'company'             => 'Kampuni',
                    'company-placeholder' => 'Chagua kampuni',
                    'color'               => 'Rangi',
                ],
            ],

            'additional' => [
                'title'       => 'Taarifa za ziada',
                'description' => 'Taarifa za ziada kuhusu idara hii.',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'         => 'Jina',
            'manager-name' => 'Meneja',
            'company-name' => 'Kampuni',
        ],

        'groups' => [
            'name'       => 'Jina',
            'manager'    => 'Meneja',
            'company'    => 'Kampuni',
            'updated-at' => 'Imesasishwa',
            'created-at' => 'Imeundwa',
        ],

        'filters' => [
            'name'         => 'Jina',
            'manager-name' => 'Meneja',
            'company-name' => 'Kampuni',
            'updated-at'   => 'Imesasishwa',
            'created-at'   => 'Imeundwa',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Idara imerejeshwa',
                    'body'  => 'Idara imerejeshwa kikamilifu.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Idara imefutwa',
                    'body'  => 'Idara imefutwa kikamilifu.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Idara imefutwa kabisa',
                    'body'  => 'Idara imefutwa kabisa.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Idara zimerejeshwa',
                    'body'  => 'Idara zimerejeshwa kikamilifu.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Idara zimefutwa',
                    'body'  => 'Idara zimefutwa kikamilifu.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Idara zimefutwa kabisa',
                    'body'  => 'Idara zimefutwa kabisa.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Jumla',

                'entries' => [
                    'name'            => 'Jina',
                    'manager'         => 'Meneja',
                    'company'         => 'Kampuni',
                    'color'           => 'Rangi',
                    'hierarchy-title' => 'Muundo wa idara',
                ],
            ],
        ],
    ],
];
