<?php

return [
    'heading' => [
        'title' => 'Muhtasari wa likizo',
    ],

    'modal-actions' => [
        'edit' => [
            'title'        => 'Hariri',
            'notification' => [
                'title' => 'Likizo imesasishwa',
                'body'  => 'Ombi la likizo limesasishwa kwa mafanikio.',
            ],
        ],

        'delete' => [
            'title' => 'Futa',
        ],
    ],

    'view-action' => [
        'title'       => 'Tazama',
        'description' => 'Tazama ombi la likizo',
    ],

    'header-actions' => [
        'create' => [
            'title'       => 'Likizo mpya',
            'description' => 'Unda ombi la likizo',

            'notification' => [
                'title' => 'Likizo imeundwa',
                'body'  => 'Ombi la likizo limeundwa kwa mafanikio.',
            ],

            'employee-not-found' => [
                'notification' => [
                    'title' => 'Mfanyakazi hakupatikana',
                    'body'  => 'Tafadhali ongeza mfanyakazi kwenye wasifu wako kabla ya kuunda ombi la likizo.',
                ],
            ],
        ],
    ],

    'form' => [
        'fields' => [
            'time-off-type'     => 'Aina ya likizo',
            'request-date-from' => 'Ombi kuanzia',
            'request-date-to'   => 'Ombi hadi',
            'period'            => 'Kipindi',
            'half-day'          => 'Nusu siku',
            'requested-days'    => 'Iliyoombwa (siku/saa)',
            'description'       => 'Maelezo',
        ],
    ],

    'infolist' => [
        'entries' => [
            'time-off-type'           => 'Aina ya likizo',
            'request-date-from'       => 'Ombi kuanzia',
            'request-date-to'         => 'Ombi hadi',
            'description'             => 'Maelezo',
            'description-placeholder' => 'Hakuna maelezo yaliyotolewa',
            'duration'                => 'Muda',
            'status'                  => 'Hali',
        ],
    ],
];
