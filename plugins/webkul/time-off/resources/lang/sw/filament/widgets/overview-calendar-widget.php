<?php

return [
    'heading' => [
        'title' => 'Likizo Muhtasari',
    ],

    'modal-actions' => [
        'edit' => [
            'title'        => 'Hariri',
            'notification' => [
                'title' => 'Likizo Imesasishwa',
                'body'  => 'Likizo request ina imesasishwa successfully.',
            ],
        ],

        'delete' => [
            'title' => 'Futa',
        ],
    ],

    'view-action' => [
        'title'       => 'Angalia',
        'description' => 'Angalia Likizo Request',
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
        ],
    ],

    'form' => [
        'fields' => [
            'time-off-type'     => 'Likizo Aina',
            'request-date-from' => 'Request Tarehe Kutoka',
            'request-date-to'   => 'Request Tarehe Hadi',
            'period'            => 'Kipindi',
            'half-day'          => 'Half Siku',
            'requested-days'    => 'Requested (Siku/Saa)',
            'description'       => 'Maelezo',
        ],
    ],

    'infolist' => [
        'entries' => [
            'time-off-type'           => 'Likizo Aina',
            'request-date-from'       => 'Request Tarehe Kutoka',
            'request-date-to'         => 'Request Tarehe Hadi',
            'description'             => 'Maelezo',
            'description-placeholder' => 'Hakuna maelezo provided',
            'duration'                => 'Duration',
            'status'                  => 'Hali',
        ],
    ],
];
