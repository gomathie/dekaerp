<?php

return [
    'title' => 'Teams',

    'navigation' => [
        'title' => 'Teams',
    ],

    'form' => [
        'fields' => [
            'name' => 'Jina',
        ],
    ],

    'table' => [
        'columns' => [
            'name'       => 'Jina',
            'created-by' => 'Imeundwa Na',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Timu imesasishwa',
                    'body'  => 'timu ina imesasishwa successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Timu imefutwa',
                    'body'  => 'timu ina imefutwa successfully.',
                ],
            ],
        ],

        'empty-state-actions' => [
            'create' => [
                'notification' => [
                    'title' => 'Teams imeundwa',
                    'body'  => 'teams ina imeundwa successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'entries' => [
                'name'          => 'Jina',
                'job-title'     => 'Kazi Kichwa',
                'work-email'    => 'Work Barua pepe',
                'work-mobile'   => 'Work Mobile',
                'work-phone'    => 'Work Simu',
                'manager'       => 'Meneja',
                'department'    => 'Idara',
                'job-position'  => 'Kazi Cheo',
                'team-tags'     => 'Timu Lebo',
                'coach'         => 'Coach',
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'name' => 'Jina',
        ],
    ],
];
