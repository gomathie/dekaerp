<?php

return [
    'title' => 'Likizo',

    'model-label' => 'Likizo',

    'navigation' => [
        'title' => 'Likizo',
    ],

    'global-search' => [
        'employee'      => 'Mfanyakazi',
        'time-off-type' => 'Aina ya likizo',
        'date-from'     => 'Tarehe kuanzia',
        'date-to'       => 'Tarehe hadi',
    ],

    'form' => [
        'fields' => [
            'employee-name'     => 'Jina la mfanyakazi',
            'department-name'   => 'Jina la idara',
            'time-off-type'     => 'Aina ya likizo',
            'date'              => 'Tarehe',
            'dates'             => 'Tarehe',
            'request-date-from' => 'Ombi kuanzia',
            'request-date-to'   => 'Ombi hadi',
            'description'       => 'Maelezo',
            'period'            => 'Kipindi',
            'half-day'          => 'Nusu siku',
            'requested-days'    => 'Iliyoombwa (siku/saa)',
            'description'       => 'Maelezo',
            'attachment'        => 'Kiambatisho',
            'day'               => 'Siku :day',
            'days'              => 'Siku :days',
        ],
    ],

    'table' => [
        'columns' => [
            'employee-name'  => 'Mfanyakazi',
            'time-off-type'  => 'Aina ya likizo',
            'description'    => 'Maelezo',
            'date-from'      => 'Tarehe kuanzia',
            'date-to'        => 'Tarehe hadi',
            'duration'       => 'Muda',
            'status'         => 'Hali',
        ],

        'groups' => [
            'employee-name' => 'Mfanyakazi',
            'time-off-type' => 'Aina ya likizo',
            'status'        => 'Hali',
            'start-date'    => 'Tarehe ya kuanza',
            'start-to'      => 'Tarehe ya kumalizika',
            'updated-at'    => 'Ilisasishwa',
            'created-at'    => 'Iliundwa',
        ],

        'actions' => [
            'approve' => [
                'title' => [
                    'validate' => 'Thibitisha',
                    'approve'  => 'Idhinisha',
                ],
                'notification' => [
                    'title' => 'Likizo imeidhinishwa',
                    'body'  => 'Likizo imeidhinishwa kwa mafanikio.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Likizo imefutwa',
                    'body'  => 'Likizo imefutwa kwa mafanikio.',
                ],
            ],

            'refused' => [
                'title'        => 'Kataa',
                'notification' => [
                    'title' => 'Likizo imekataliwa',
                    'body'  => 'Likizo imekataliwa kwa mafanikio.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Likizo imefutwa',
                    'body'  => 'Likizo imefutwa kwa mafanikio.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'employee-name'     => 'Jina la mfanyakazi',
            'department-name'   => 'Jina la idara',
            'time-off-type'     => 'Aina ya likizo',
            'date'              => 'Tarehe',
            'dates'             => 'Tarehe',
            'request-date-from' => 'Ombi kuanzia',
            'request-date-to'   => 'Ombi hadi',
            'description'       => 'Maelezo',
            'period'            => 'Kipindi',
            'half-day'          => 'Nusu siku',
            'requested-days'    => 'Iliyoombwa (siku/saa)',
            'attachment'        => 'Kiambatisho',
            'day'               => 'Siku :day',
            'days'              => 'Siku :days',
            'date-from'         => 'Tarehe kuanzia',
            'date-to'           => 'Tarehe hadi',
            'status'            => 'Hali',
        ],
    ],
];
