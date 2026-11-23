<?php

return [
    'title' => 'Ankara',

    'navigation' => [
        'title' => 'Ankara',
        'group' => 'Ankara',
    ],

    'form' => [
        'section' => [
            'general' => [
                'title'  => 'Jumla',
                'fields' => [
                    'vendor-credit-note' => 'Msambazaji Noti ya mkopo',
                    'vendor'             => 'Msambazaji',
                    'bill-date'          => 'Bili Tarehe',
                    'bill-reference'     => 'Bili Kumbukumbu',
                    'accounting-date'    => 'Uhasibu Tarehe',
                    'payment-reference'  => 'Malipo Kumbukumbu',
                    'recipient-bank'     => 'Mpokeaji Benki',
                    'due-date'           => 'Tarehe ya malipo',
                    'payment-term'       => 'Masharti ya malipo',
                ],
            ],
        ],

        'tabs' => [
            'invoice-lines' => [
                'title' => 'Mistari ya ankara',

                'repeater' => [
                    'products' => [
                        'title'       => 'Bidhaa',
                        'add-product' => 'Ongeza bidhaa',

                        'fields' => [
                            'product'             => 'Bidhaa',
                            'quantity'            => 'Kiasi',
                            'unit'                => 'Uniti',
                            'taxes'               => 'Kodi',
                            'discount-percentage' => 'Punguzo',
                            'unit-price'          => 'Bei ya uniti',
                            'sub-total'           => 'Jumla ndogo',
                        ],
                    ],
                ],
            ],

            'other-information' => [
                'title'    => 'Nyingine Taarifa',
                'fieldset' => [
                    'accounting' => [
                        'title' => 'Uhasibu',

                        'fields' => [
                            'incoterm'          => 'Incoterm',
                            'incoterm-location' => 'Incoterm Mahali',
                        ],
                    ],

                    'secured' => [
                        'title'  => 'Secured',
                        'fields' => [
                            'payment-method' => 'Malipo Method',
                            'auto-post'      => 'Auto Post',
                            'checked'        => 'Checked',
                        ],
                    ],

                    'additional-information' => [
                        'title'  => 'Additional Taarifa',
                        'fields' => [
                            'company'  => 'Kampuni',
                            'currency' => 'Sarafu',
                        ],
                    ],
                ],
            ],

            'term-and-conditions' => [
                'title' => 'Term & Conditions',
            ],
        ],
    ],

    'infolist' => [
        'section' => [
            'general' => [
                'title'   => 'Jumla',
                'entries' => [
                    'vendor-invoice'    => 'Msambazaji Ankara',
                    'vendor'            => 'Msambazaji',
                    'bill-date'         => 'Bili Tarehe',
                    'bill-reference'    => 'Bili Kumbukumbu',
                    'accounting-date'   => 'Uhasibu Tarehe',
                    'payment-reference' => 'Malipo Kumbukumbu',
                    'recipient-bank'    => 'Mpokeaji Benki',
                    'due-date'          => 'Tarehe ya malipo',
                    'payment-term'      => 'Masharti ya malipo',
                ],
            ],
        ],

        'tabs' => [
            'invoice-lines' => [
                'title' => 'Mistari ya ankara',

                'repeater' => [
                    'products' => [
                        'title'       => 'Bidhaa',
                        'add-product' => 'Ongeza bidhaa',

                        'entries' => [
                            'product'             => 'Bidhaa',
                            'quantity'            => 'Kiasi',
                            'unit'                => 'Uniti',
                            'taxes'               => 'Kodi',
                            'discount-percentage' => 'Punguzo',
                            'unit-price'          => 'Bei ya uniti',
                            'sub-total'           => 'Jumla ndogo',
                        ],
                    ],
                ],
            ],

            'other-information' => [
                'title'    => 'Nyingine Taarifa',
                'fieldset' => [
                    'accounting' => [
                        'title' => 'Uhasibu',

                        'entries' => [
                            'incoterm'          => 'Incoterm',
                            'incoterm-location' => 'Incoterm Mahali',
                        ],
                    ],

                    'secured' => [
                        'title'   => 'Secured',
                        'entries' => [
                            'payment-method' => 'Malipo Method',
                            'auto-post'      => 'Auto Post',
                            'checked'        => 'Checked',
                        ],
                    ],

                    'additional-information' => [
                        'title'   => 'Additional Taarifa',
                        'entries' => [
                            'company'  => 'Kampuni',
                            'currency' => 'Sarafu',
                        ],
                    ],
                ],
            ],

            'term-and-conditions' => [
                'title' => 'Term & Conditions',
            ],
        ],
    ],
];
