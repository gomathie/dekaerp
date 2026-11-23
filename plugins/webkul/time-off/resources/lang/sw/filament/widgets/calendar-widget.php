<?php

return [
    'heading' => [
        'title' => 'Likizo Requests',
    ],

    'modal-actions' => [
        'edit' => [
            'title'                         => 'Hariri',
            'duration-display'              => ':count working siku|:count working siku',
            'duration-display-with-weekend' => ':count working siku (+ :weekend weekend siku)|:count working siku (+ :weekend weekend siku)',

            'notification' => [
                'title' => 'Likizo Imesasishwa',
                'body'  => 'Yako likizo request ina imesasishwa successfully.',
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
        'title'       => 'Angalia',
        'description' => 'Angalia Likizo Request',
    ],

    'notifications' => [
        'employee-not-found' => [
            'title' => 'Mfanyakazi Si Imepatikana',
            'body'  => 'Tafadhali ongeza mfanyakazi hadi yako wasifu kabla ya requesting likizo.',
        ],

        'error' => [
            'title' => 'Something went wrong',
            'body'  => 'Sisi could si process yako likizo request. Tafadhali jaribu tena.',
        ],
    ],

    'header-actions' => [
        'create' => [
            'title'       => 'Mpya Likizo',
            'description' => 'Unda Likizo Request',

            'notification' => [
                'title' => 'Likizo Imeundwa',
                'body'  => 'Likizo request ina imeundwa successfully.',
            ],

            'employee-not-found' => [
                'notification' => [
                    'title' => 'Mfanyakazi Si Imepatikana',
                    'body'  => 'Tafadhali ongeza mfanyakazi hadi yako wasifu kabla ya creating likizo request.',
                ],
            ],

            'success' => [
                'notification' => [
                    'title' => 'Likizo Imeundwa',
                    'body'  => 'Yako likizo request ina imeundwa successfully.',
                ],
            ],
        ],
    ],

    'form' => [
        'title'       => 'Likizo Request',
        'description' => 'Unda au hariri yako likizo request na ifuatayo maelezo:',

        'fields' => [
            'time-off-type'             => 'Likizo Aina',
            'time-off-type-placeholder' => 'Chagua likizo aina',
            'time-off-type-helper'      => 'Chagua aina ya likizo wewe ni requesting.',
            'request-date-from'         => 'Request Tarehe Kutoka',
            'request-date-to'           => 'Request Tarehe Hadi',
            'period'                    => 'Kipindi',
            'half-day'                  => 'Half Siku',
            'half-day-helper'           => 'Toggle kwa half-siku likizo.',
            'requested-days'            => 'Requested (Siku/Saa)',
            'description'               => 'Maelezo',
            'description-placeholder'   => 'Hakuna maelezo provided',
            'description-helper'        => 'Provide brief maelezo ya yako likizo request.',
            'duration'                  => 'Duration',
            'please-select-dates'       => 'Tafadhali chagua request tarehe kutoka na hadi.',
        ],
    ],

    'infolist' => [
        'title'       => 'Likizo Maelezo',
        'description' => 'Hapa ni maelezo ya yako likizo request:',
        'entries'     => [
            'time-off-type'           => 'Likizo Aina',
            'request-date-from'       => 'Request Tarehe Kutoka',
            'request-date-to'         => 'Request Tarehe Hadi',
            'description'             => 'Maelezo',
            'description-placeholder' => 'Hakuna maelezo provided',
            'duration'                => 'Duration',
            'status'                  => 'Hali',
        ],
    ],

    'events' => [
        'title' => ':name Washa :status: :days siku(s)',
    ],
];
