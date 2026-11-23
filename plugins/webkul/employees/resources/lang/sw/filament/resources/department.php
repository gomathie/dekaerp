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
                'title' => 'Jumla Taarifa',

                'fields' => [
                    'name'                => 'Jina',
                    'manager'             => 'Meneja',
                    'parent-department'   => 'Parent Idara',
                    'manager-placeholder' => 'Chagua Meneja',
                    'company'             => 'Kampuni',
                    'company-placeholder' => 'Chagua Kampuni',
                    'color'               => 'Rangi',
                ],
            ],

            'additional' => [
                'title'       => 'Additional Taarifa',
                'description' => 'Additional taarifa kuhusu hii idara.',
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
                    'title' => 'Idara restored',
                    'body'  => 'idara ina restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Idara imefutwa',
                    'body'  => 'idara ina imefutwa successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Idara force imefutwa',
                    'body'  => 'idara ina force imefutwa successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Idara restored',
                    'body'  => 'idara ina restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Idara imefutwa',
                    'body'  => 'idara ina imefutwa successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Idara force imefutwa',
                    'body'  => 'idara ina force imefutwa successfully.',
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
                    'hierarchy-title' => 'Idara Organization',
                ],
            ],
        ],
    ],
];
