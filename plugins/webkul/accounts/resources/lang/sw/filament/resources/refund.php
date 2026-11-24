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
                'title'  => 'Taarifa za jumla',
                'fields' => [
                    'vendor-credit-note' => 'Hati ya mkopo ya msambazaji',
                    'vendor'             => 'Msambazaji',
                    'bill-date'          => 'Tarehe ya bili',
                    'bill-reference'     => 'Rejea ya bili',
                    'accounting-date'    => 'Tarehe ya uhasibu',
                    'payment-reference'  => 'Rejea ya malipo',
                    'recipient-bank'     => 'Benki ya mpokeaji',
                    'due-date'           => 'Tarehe ya mwisho',
                    'payment-term'       => 'Sharti la malipo',
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
                            'quantity'            => 'Idadi',
                            'unit'                => 'Kipimo',
                            'taxes'               => 'Kodi',
                            'discount-percentage' => 'Asilimia ya punguzo',
                            'unit-price'          => 'Bei ya kipimo',
                            'sub-total'           => 'Jumla ndogo',
                        ],
                    ],
                ],
            ],

            'other-information' => [
                'title'    => 'Taarifa nyingine',
                'fieldset' => [
                    'accounting' => [
                        'title' => 'Uhasibu',

                        'fields' => [
                            'incoterm'          => 'Incoterm',
                            'incoterm-location' => 'Mahali pa Incoterm',
                        ],
                    ],

                    'secured' => [
                        'title'  => 'Imelindwa',
                        'fields' => [
                            'payment-method' => 'Njia ya malipo',
                            'auto-post'      => 'Rekodi kiotomatiki',
                            'checked'        => 'Imekaguliwa',
                        ],
                    ],

                    'additional-information' => [
                        'title'  => 'Taarifa za ziada',
                        'fields' => [
                            'company'  => 'Kampuni',
                            'currency' => 'Sarafu',
                        ],
                    ],
                ],
            ],

            'term-and-conditions' => [
                'title' => 'Sheria na masharti',
            ],
        ],
    ],

    'infolist' => [
        'section' => [
            'general' => [
                'title'   => 'Taarifa za jumla',
                'entries' => [
                    'vendor-invoice'    => 'Ankara ya msambazaji',
                    'vendor'            => 'Msambazaji',
                    'bill-date'         => 'Tarehe ya bili',
                    'bill-reference'    => 'Rejea ya bili',
                    'accounting-date'   => 'Tarehe ya uhasibu',
                    'payment-reference' => 'Rejea ya malipo',
                    'recipient-bank'    => 'Benki ya mpokeaji',
                    'due-date'          => 'Tarehe ya mwisho',
                    'payment-term'      => 'Sharti la malipo',
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
                            'quantity'            => 'Idadi',
                            'unit'                => 'Kipimo',
                            'taxes'               => 'Kodi',
                            'discount-percentage' => 'Asilimia ya punguzo',
                            'unit-price'          => 'Bei ya kipimo',
                            'sub-total'           => 'Jumla ndogo',
                        ],
                    ],
                ],
            ],

            'other-information' => [
                'title'    => 'Taarifa nyingine',
                'fieldset' => [
                    'accounting' => [
                        'title' => 'Uhasibu',

                        'entries' => [
                            'incoterm'          => 'Incoterm',
                            'incoterm-location' => 'Mahali pa Incoterm',
                        ],
                    ],

                    'secured' => [
                        'title'   => 'Imelindwa',
                        'entries' => [
                            'payment-method' => 'Njia ya malipo',
                            'auto-post'      => 'Rekodi kiotomatiki',
                            'checked'        => 'Imekaguliwa',
                        ],
                    ],

                    'additional-information' => [
                        'title'   => 'Taarifa za ziada',
                        'entries' => [
                            'company'  => 'Kampuni',
                            'currency' => 'Sarafu',
                        ],
                    ],
                ],
            ],

            'term-and-conditions' => [
                'title' => 'Sheria na masharti',
            ],
        ],
    ],
];
