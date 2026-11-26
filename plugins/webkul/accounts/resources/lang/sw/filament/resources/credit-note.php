<?php

return [
    'title' => 'Hati ya mkopo',

    'navigation' => [
        'title' => 'Hati za mkopo',
        'group' => 'Ankara',
    ],

    'form' => [
        'section' => [
            'general' => [
                'title'  => 'Taarifa za jumla',
                'fields' => [
                    'customer-invoice' => 'Hati ya mkopo ya mteja',
                    'customer'         => 'Mteja',
                    'invoice-date'     => 'Tarehe ya ankara',
                    'due-date'         => 'Tarehe ya mwisho',
                    'payment-term'     => 'Sharti la malipo',
                ],
            ],
        ],

        'tabs' => [
            'invoice-lines' => [
                'title' => 'Mistari ya ankara',

                'repeater' => [
                    'products' => [
                        'title'       => 'Bidhaa',
                        'add-product' => 'Ongeza kipengee',

                        'fields' => [
                            'product'             => 'Bidhaa / huduma',
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
                    'invoice' => [
                        'title'  => 'Ankara',
                        'fields' => [
                            'customer-reference' => 'Rejea ya mteja',
                            'sales-person'       => 'Muuzaji',
                            'payment-reference'  => 'Rejea ya malipo',
                            'recipient-bank'     => 'Benki ya mpokeaji',
                            'delivery-date'      => 'Tarehe ya uwasilishaji',
                        ],
                    ],

                    'accounting' => [
                        'title' => 'Uhasibu',

                        'fields' => [
                            'incoterm'          => 'Incoterm',
                            'incoterm-location' => 'Mahali pa Incoterm',
                            'payment-method'    => 'Njia ya malipo',
                            'auto-post'         => 'Rekodi kiotomatiki',
                            'checked'           => 'Imekaguliwa',
                        ],
                    ],

                    'additional-information' => [
                        'title'  => 'Taarifa za ziada',
                        'fields' => [
                            'company'  => 'Kampuni',
                            'currency' => 'Sarafu',
                        ],
                    ],

                    'marketing' => [
                        'title'  => 'Masoko',
                        'fields' => [
                            'campaign' => 'Kampeni',
                            'medium'   => 'Njia',
                            'source'   => 'Chanzo',
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
                    'customer-invoice' => 'Hati ya mkopo ya mteja',
                    'customer'         => 'Mteja',
                    'invoice-date'     => 'Tarehe ya ankara',
                    'due-date'         => 'Tarehe ya mwisho',
                    'payment-term'     => 'Sharti la malipo',
                ],
            ],
        ],

        'tabs' => [
            'invoice-lines' => [
                'title' => 'Mistari ya ankara',

                'repeater' => [
                    'products' => [
                        'entries' => [
                            'product'             => 'Bidhaa / huduma',
                            'quantity'            => 'Idadi',
                            'unit'                => 'Kipimo',
                            'taxes'               => 'Kodi',
                            'discount-percentage' => 'Asilimia ya punguzo',
                            'unit-price'          => 'Bei ya kipimo',
                            'sub-total'           => 'Jumla ndogo',
                            'total'               => 'Jumla',
                        ],
                    ],
                ],
            ],

            'other-information' => [
                'title'    => 'Taarifa nyingine',
                'fieldset' => [
                    'invoice' => [
                        'title'   => 'Ankara',
                        'entries' => [
                            'customer-reference' => 'Rejea ya mteja',
                            'sales-person'       => 'Muuzaji',
                            'payment-reference'  => 'Rejea ya malipo',
                            'recipient-bank'     => 'Benki ya mpokeaji',
                            'delivery-date'      => 'Tarehe ya uwasilishaji',
                        ],
                    ],

                    'accounting' => [
                        'title' => 'Uhasibu',

                        'fieldset' => [
                            'incoterm'          => 'Incoterm',
                            'incoterm-location' => 'Mahali pa Incoterm',
                            'payment-method'    => 'Njia ya malipo',
                            'auto-post'         => 'Rekodi kiotomatiki',
                            'checked'           => 'Imekaguliwa',
                        ],
                    ],

                    'additional-information' => [
                        'title'   => 'Taarifa za ziada',
                        'entries' => [
                            'company'  => 'Kampuni',
                            'currency' => 'Sarafu',
                        ],
                    ],

                    'marketing' => [
                        'title'   => 'Masoko',
                        'entries' => [
                            'campaign' => 'Kampeni',
                            'medium'   => 'Njia',
                            'source'   => 'Chanzo',
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
