<?php

return [
    'heading' => [
        'title' => 'Maombi ya likizo',
    ],

    'modal-actions' => [
        'edit' => [
            'title'                         => 'Hariri',
            'duration-display'              => 'Siku :count ya kazi|Siku :count za kazi',
            'duration-display-with-weekend' => 'Siku :count ya kazi (+ siku :weekend ya mwisho wa wiki)|Siku :count za kazi (+ siku :weekend za mwisho wa wiki)',

            'notification' => [
                'title' => 'Likizo imesasishwa',
                'body'  => 'Ombi lako la likizo limesasishwa kwa mafanikio.',
            ],
        ],

        'delete' => [
            'title' => 'Futa',
        ],
    ],

    'config' => [
        'button-text' => [
            'today' => 'Leo',
            'month' => 'Mwezi',
            'week'  => 'Wiki',
            'list'  => 'Orodha',
        ],
    ],

    'view-action' => [
        'title'       => 'Tazama',
        'description' => 'Tazama ombi la likizo',
    ],

    'notifications' => [
        'employee-not-found' => [
            'title' => 'Mfanyakazi hakupatikana',
            'body'  => 'Tafadhali ongeza mfanyakazi kwenye wasifu wako kabla ya kuomba likizo.',
        ],

        'error' => [
            'title' => 'Hitilafu fulani imetokea',
            'body'  => 'Hatukuweza kushughulikia ombi lako la likizo. Tafadhali jaribu tena.',
        ],
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

            'success' => [
                'notification' => [
                    'title' => 'Likizo imeundwa',
                    'body'  => 'Ombi lako la likizo limeundwa kwa mafanikio.',
                ],
            ],
        ],
    ],

    'form' => [
        'title'       => 'Ombi la likizo',
        'description' => 'Unda au hariri ombi lako la likizo kwa maelezo yafuatayo:',

        'fields' => [
            'time-off-type'             => 'Aina ya likizo',
            'time-off-type-placeholder' => 'Chagua aina ya likizo',
            'time-off-type-helper'      => 'Chagua aina ya likizo unayoomba.',
            'request-date-from'         => 'Ombi kuanzia',
            'request-date-to'           => 'Ombi hadi',
            'period'                    => 'Kipindi',
            'half-day'                  => 'Nusu siku',
            'half-day-helper'           => 'Washa kwa likizo ya nusu siku.',
            'requested-days'            => 'Iliyoombwa (siku/saa)',
            'description'               => 'Maelezo',
            'description-placeholder'   => 'Hakuna maelezo yaliyotolewa',
            'description-helper'        => 'Toa maelezo mafupi ya ombi lako la likizo.',
            'duration'                  => 'Muda',
            'please-select-dates'       => 'Tafadhali chagua tarehe ya kuanzia na ya mwisho ya ombi.',
        ],
    ],

    'infolist' => [
        'title'       => 'Maelezo ya likizo',
        'description' => 'Haya ni maelezo ya ombi lako la likizo:',
        'entries'     => [
            'time-off-type'           => 'Aina ya likizo',
            'request-date-from'       => 'Ombi kuanzia',
            'request-date-to'         => 'Ombi hadi',
            'description'             => 'Maelezo',
            'description-placeholder' => 'Hakuna maelezo yaliyotolewa',
            'duration'                => 'Muda',
            'status'                  => 'Hali',
        ],
    ],

    'events' => [
        'title' => ':name — :status: siku :days',
    ],
];
