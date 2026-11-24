<?php

return [
    'title' => 'Timu',

    'navigation' => [
        'title' => 'Timu',
    ],

    'form' => [
        'fields' => [
            'name' => 'Jina',
        ],
    ],

    'table' => [
        'columns' => [
            'name'       => 'Jina',
            'created-by' => 'Imeundwa na',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Timu imesasishwa',
                    'body'  => 'Timu imesasishwa kwa mafanikio.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Timu imefutwa',
                    'body'  => 'Timu imefutwa kwa mafanikio.',
                ],
            ],
        ],

        'empty-state-actions' => [
            'create' => [
                'notification' => [
                    'title' => 'Timu zimeundwa',
                    'body'  => 'Timu zimeundwa kwa mafanikio.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'entries' => [
                'name'          => 'Jina',
                'job-title'     => 'Cheo',
                'work-email'    => 'Barua pepe ya kazini',
                'work-mobile'   => 'Simu ya mkononi ya kazini',
                'work-phone'    => 'Simu ya kazini',
                'manager'       => 'Meneja',
                'department'    => 'Idara',
                'job-position'  => 'Nafasi ya kazi',
                'team-tags'     => 'Lebo za timu',
                'coach'         => 'Mlezi',
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'name' => 'Jina',
        ],
    ],
];
