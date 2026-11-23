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
                    'customer-invoice' => 'Mteja Noti ya mkopo',
                    'customer'         => 'Mteja',
                    'invoice-date'     => 'Tarehe ya ankara',
                    'due-date'         => 'Tarehe ya malipo',
                    'payment-term'     => 'Masharti ya malipo',
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
                    'invoice' => [
                        'title'  => 'Ankara',
                        'fields' => [
                            'customer-reference' => 'Kumbukumbu ya mteja',
                            'sales-person'       => 'Mauzo Person',
                            'payment-reference'  => 'Malipo Kumbukumbu',
                            'recipient-bank'     => 'Mpokeaji Benki',
                            'delivery-date'      => 'Usafirishaji Tarehe',
                        ],
                    ],

                    'accounting' => [
                        'title' => 'Uhasibu',

                        'fields' => [
                            'incoterm'          => 'Incoterm',
                            'incoterm-location' => 'Incoterm Mahali',
                            'payment-method'    => 'Malipo Method',
                            'auto-post'         => 'Auto Post',
                            'checked'           => 'Checked',
                        ],
                    ],

                    'additional-information' => [
                        'title'  => 'Additional Taarifa',
                        'fields' => [
                            'company'  => 'Kampuni',
                            'currency' => 'Sarafu',
                        ],
                    ],

                    'marketing' => [
                        'title'  => 'Marketing',
                        'fields' => [
                            'campaign' => 'Campaign',
                            'medium'   => 'Wastani',
                            'source'   => 'Chanzo',
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
                    'customer-invoice' => 'Mteja Noti ya mkopo',
                    'customer'         => 'Mteja',
                    'invoice-date'     => 'Tarehe ya ankara',
                    'due-date'         => 'Tarehe ya malipo',
                    'payment-term'     => 'Masharti ya malipo',
                ],
            ],
        ],

        'tabs' => [
            'invoice-lines' => [
                'title' => 'Mistari ya ankara',

                'repeater' => [
                    'products' => [
                        'entries' => [
                            'product'             => 'Bidhaa',
                            'quantity'            => 'Kiasi',
                            'unit'                => 'Kipimo',
                            'taxes'               => 'Kodi',
                            'discount-percentage' => 'Punguzo',
                            'unit-price'          => 'Bei ya uniti',
                            'sub-total'           => 'Jumla ndogo',
                            'total'               => 'Jumla',
                        ],
                    ],
                ],
            ],

            'other-information' => [
                'title'    => 'Nyingine Taarifa',
                'fieldset' => [
                    'invoice' => [
                        'title'   => 'Ankara',
                        'entries' => [
                            'customer-reference' => 'Kumbukumbu ya mteja',
                            'sales-person'       => 'Mauzo Person',
                            'payment-reference'  => 'Malipo Kumbukumbu',
                            'recipient-bank'     => 'Mpokeaji Benki',
                            'delivery-date'      => 'Usafirishaji Tarehe',
                        ],
                    ],

                    'accounting' => [
                        'title' => 'Uhasibu',

                        'fieldset' => [
                            'incoterm'          => 'Incoterm',
                            'incoterm-location' => 'Incoterm Mahali',
                            'payment-method'    => 'Malipo Method',
                            'auto-post'         => 'Auto Post',
                            'checked'           => 'Checked',
                        ],
                    ],

                    'additional-information' => [
                        'title'   => 'Additional Taarifa',
                        'entries' => [
                            'company'  => 'Kampuni',
                            'currency' => 'Sarafu',
                        ],
                    ],

                    'marketing' => [
                        'title'   => 'Marketing',
                        'entries' => [
                            'campaign' => 'Campaign',
                            'medium'   => 'Wastani',
                            'source'   => 'Chanzo',
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
