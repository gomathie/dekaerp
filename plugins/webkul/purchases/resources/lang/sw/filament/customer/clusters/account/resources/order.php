<?php

return [
    'table' => [
        'columns' => [
            'reference'         => 'Kumbukumbu',
            'total-amount'      => 'Jumla ya kiasi',
            'confirmation-date' => 'Tarehe ya uthibitisho',
            'status'            => 'Hali',
        ],
    ],

    'products' => [
        'columns' => [
            'product'    => 'Bidhaa',
            'quantity'   => 'Idadi',
            'unit-price' => 'Bei ya kipimo',
            'taxes'      => 'Kodi',
            'discount'   => 'Punguzo %',
            'amount'     => 'Kiasi',
        ],
    ],

    'infolist' => [
        'settings' => [
            'entries' => [
                'buyer' => 'Mnunuzi',
            ],

            'actions' => [
                'accept' => [
                    'label' => 'Kubali',

                    'notification' => [
                        'title' => 'Nukuu ya bei imekubaliwa',
                        'body'  => 'Ombi la nukuu limekubaliwa.',
                    ],

                    'message' => [
                        'body' => 'Msambazaji amekubali ombi la nukuu.',
                    ],
                ],

                'decline' => [
                    'label' => 'Kataa',

                    'notification' => [
                        'title' => 'Nukuu ya bei imekataliwa',
                        'body'  => 'Ombi la nukuu limekataliwa.',
                    ],

                    'message' => [
                        'body' => 'Msambazaji amekataa ombi la nukuu.',
                    ],
                ],

                'print' => [
                    'label' => 'Pakua/Chapisha',
                ],
            ],
        ],

        'general' => [
            'entries' => [
                'purchase-order'        => 'Oda ya ununuzi #:id',
                'quotation'             => 'Ombi la nukuu ya bei #:id',
                'order-date'            => 'Tarehe ya oda',
                'from'                  => 'Kuanzia',
                'confirmation-date'     => 'Tarehe ya uthibitisho',
                'receipt-date'          => 'Tarehe ya upokeaji',
                'products'              => 'Bidhaa',
                'untaxed-amount'        => 'Kiasi bila kodi',
                'tax-amount'            => 'Kiasi cha kodi',
                'total'                 => 'Jumla',
                'communication-history' => 'Historia ya mawasiliano',
            ],
        ],
    ],
];
