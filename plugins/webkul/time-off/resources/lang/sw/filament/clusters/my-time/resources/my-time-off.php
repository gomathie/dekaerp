<?php

return [
    'title' => 'Likizo',

    'model-label' => 'Yangu Likizo',

    'navigation' => [
        'title' => 'Yangu Likizo',
    ],

    'form' => [
        'fields' => [
            'time-off-type'     => 'Likizo Aina',
            'date'              => 'Tarehe',
            'dates'             => 'Dates',
            'request-date-from' => 'Request Tarehe Kutoka',
            'request-date-to'   => 'Request Tarehe Hadi',
            'description'       => 'Maelezo',
            'period'            => 'Kipindi',
            'half-day'          => 'Half Siku',
            'requested-days'    => 'Requested (Siku/Saa)',
            'description'       => 'Maelezo',
            'attachment'        => 'Kiambatisho',
            'day'               => ':day siku',
            'days'              => ':days siku(s)',
        ],
    ],

    'table' => [
        'columns' => [
            'employee-name'  => 'Mfanyakazi',
            'time-off-type'  => 'Likizo Aina',
            'description'    => 'Maelezo',
            'date-from'      => 'Tarehe Kutoka',
            'date-to'        => 'Tarehe Hadi',
            'duration'       => 'Duration',
            'status'         => 'Hali',
        ],

        'groups' => [
            'employee-name' => 'Mfanyakazi',
            'time-off-type' => 'Likizo Aina',
            'status'        => 'Hali',
            'start-date'    => 'Anza Tarehe',
            'start-to'      => 'Mwisho Tarehe',
            'updated-at'    => 'Imesasishwa',
            'created-at'    => 'Imeundwa',
        ],

        'actions' => [
            'approve' => [
                'title' => [
                    'validate' => 'Validate',
                    'approve'  => 'Idhinisha',
                ],
                'notification' => [
                    'title' => 'Likizo imeidhinishwa',
                    'body'  => 'likizo ina imeidhinishwa successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Likizo imefutwa',
                    'body'  => 'likizo ina imefutwa successfully.',
                ],
            ],

            'refused' => [
                'title'        => 'Kataa',
                'notification' => [
                    'title' => 'Likizo imekataliwa',
                    'body'  => 'likizo ina imekataliwa successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Likizos imefutwa',
                    'body'  => 'likizos ina imefutwa successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'time-off-type'     => 'Likizo Aina',
            'date'              => 'Tarehe',
            'dates'             => 'Dates',
            'request-date-from' => 'Request Tarehe Kutoka',
            'request-date-to'   => 'Request Tarehe Hadi',
            'description'       => 'Maelezo',
            'period'            => 'Kipindi',
            'half-day'          => 'Half Siku',
            'requested-days'    => 'Requested (Siku/Saa)',
            'description'       => 'Maelezo',
            'attachment'        => 'Kiambatisho',
            'day'               => ':day siku',
            'days'              => ':days siku(s)',
        ],
    ],
];
