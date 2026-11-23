<?php

return [
    'table' => [
        'columns' => [
            'reference'         => 'Kumbukumbu',
            'total-amount'      => 'Jumla Kiasi',
            'confirmation-date' => 'Confirmation Tarehe',
            'status'            => 'Hali',
        ],
    ],

    'products' => [
        'columns' => [
            'product'    => 'Bidhaa',
            'quantity'   => 'Kiasi',
            'unit-price' => 'Bei ya uniti',
            'taxes'      => 'Kodi',
            'discount'   => 'Punguzo %',
            'amount'     => 'Kiasi',
        ],
    ],

    'infolist' => [
        'settings' => [
            'entries' => [
                'buyer' => 'Buyer',
            ],

            'actions' => [
                'accept' => [
                    'label' => 'Accept',

                    'notification' => [
                        'title' => 'Nukuu Accepted',
                        'body'  => 'Ombi la bei ina acknowledged successfully.',
                    ],

                    'message' => [
                        'body' => 'Ombi la bei ina acknowledged na msambazaji.',
                    ],
                ],

                'decline' => [
                    'label' => 'Decline',

                    'notification' => [
                        'title' => 'Nukuu Declined',
                        'body'  => 'Ombi la bei ina declined successfully.',
                    ],

                    'message' => [
                        'body' => 'Ombi la bei ina declined na msambazaji.',
                    ],
                ],

                'print' => [
                    'label' => 'Pakua/Chapisha',
                ],
            ],
        ],

        'general' => [
            'entries' => [
                'purchase-order'        => 'Agizo la ununuzi #:id',
                'quotation'             => 'Request kwa Nukuu #:id',
                'order-date'            => 'Agizo Tarehe',
                'from'                  => 'Kutoka',
                'confirmation-date'     => 'Confirmation Tarehe',
                'receipt-date'          => 'Stakabadhi Tarehe',
                'products'              => 'Bidhaa',
                'untaxed-amount'        => 'Bila kodi Kiasi',
                'tax-amount'            => 'Kodi Kiasi',
                'total'                 => 'Jumla',
                'communication-history' => 'Mawasiliano Historia',
            ],
        ],
    ],
];
