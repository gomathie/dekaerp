<?php

return [
    'title' => 'Nukuu',

    'navigation' => [
        'title' => 'Nukuu',
    ],

    'global-search' => [
        'customer'  => 'Mteja',
        'reference' => 'Kumbukumbu',
        'amount'    => 'Kiasi',
    ],

    'form' => [
        'section' => [
            'general' => [
                'title'  => 'Jumla',
                'fields' => [
                    'customer'       => 'Mteja',
                    'expiration'     => 'Expiration',
                    'quotation-date' => 'Nukuu Tarehe',
                    'order-date'     => 'Agizo Tarehe',
                    'payment-term'   => 'Masharti ya malipo',
                ],
            ],
        ],

        'tabs' => [
            'order-line' => [
                'title' => 'Agizo Mstari',

                'repeater' => [
                    'products' => [
                        'title'       => 'Bidhaa',
                        'add-product' => 'Ongeza bidhaa',

                        'columns'     => [
                            'product'                    => 'Bidhaa',
                            'product-variants'           => 'Bidhaa Variants',
                            'product-simple'             => 'Bidhaa Simple',
                            'quantity'                   => 'Kiasi',
                            'insufficient-stock-tooltip' => 'Insufficient hisa hadi fulfill hii mahitaji.',
                            'uom'                        => 'UOM',
                            'lead-time'                  => 'Lead Time',
                            'qty-delivered'              => 'Delivered',
                            'qty-invoiced'               => 'Invoiced',
                            'packaging-qty'              => 'Kifungashio Kiasi',
                            'packaging'                  => 'Kifungashio',
                            'unit-price'                 => 'Bei ya uniti',
                            'cost'                       => 'Gharama',
                            'margin'                     => 'Pembe',
                            'taxes'                      => 'Kodi',
                            'amount'                     => 'Kiasi',
                            'margin-percentage'          => 'Pembe (%)',
                            'discount-percentage'        => 'Punguzo (%)',
                        ],

                        'fields'      => [
                            'product'             => 'Bidhaa',
                            'product-variants'    => 'Bidhaa Variants',
                            'product-simple'      => 'Bidhaa Simple',
                            'quantity'            => 'Kiasi',
                            'uom'                 => 'Kipimo',
                            'lead-time'           => 'Lead Time',
                            'qty-delivered'       => 'Kiasi Delivered',
                            'qty-invoiced'        => 'Kiasi Invoiced',
                            'packaging-qty'       => 'Kifungashio Kiasi',
                            'packaging'           => 'Kifungashio',
                            'unit-price'          => 'Bei ya uniti',
                            'cost'                => 'Gharama',
                            'margin'              => 'Pembe',
                            'taxes'               => 'Kodi',
                            'amount'              => 'Kiasi',
                            'margin-percentage'   => 'Pembe (%)',
                            'discount-percentage' => 'Punguzo (%)',
                        ],

                        'notifications' => [
                            'quantity-below-delivered' => [
                                'title' => 'Haiwezekani Reduce Kiasi',
                                'body'  => 'Wewe haiwezekani reduce kiasi chini ya delivered kiasi (:qty).',
                            ],
                        ],

                        'delete-action' => [
                            'error' => [
                                'title' => 'Haiwezekani Futa Bidhaa',
                                'body'  => 'Bidhaa haiwezekani kuwa imefutwa kutoka imethibitishwa agizo la mauzo.',
                            ],
                        ],

                        'actions' => [
                            'open-product' => [
                                'tooltip' => 'Wazi bidhaa',
                            ],
                        ],
                    ],

                    'product-optional' => [
                        'title'       => 'Si lazima Bidhaa',
                        'add-product' => 'Ongeza bidhaa',

                        'columns' => [
                            'product'             => 'Bidhaa',
                            'description'         => 'Maelezo',
                            'quantity'            => 'Kiasi',
                            'uom'                 => 'Kipimo',
                            'unit-price'          => 'Bei ya uniti',
                            'discount-percentage' => 'Punguzo (%)',
                        ],

                        'fields'      => [
                            'product'             => 'Bidhaa',
                            'description'         => 'Maelezo',
                            'quantity'            => 'Kiasi',
                            'uom'                 => 'Kipimo',
                            'unit-price'          => 'Bei ya uniti',
                            'discount-percentage' => 'Punguzo (%)',

                            'actions' => [
                                'tooltip' => [
                                    'add-order-line' => 'Ongeza Agizo Mstari',
                                    'already-added'  => 'Tayari Added Hadi Agizo',
                                ],

                                'notifications' => [
                                    'product-added' => [
                                        'title' => 'Bidhaa added',
                                        'body'  => 'bidhaa ina added successfully.',
                                    ],

                                    'product-not-found' => [
                                        'title' => 'Bidhaa Si Imepatikana',
                                    ],

                                    'product-already-exists' => [
                                        'title' => 'Bidhaa Tayari Ipo',
                                        'body'  => 'Hii bidhaa ni tayari katika agizo mistari. Tafadhali sasisha existing mstari instead.',
                                    ],

                                    'missing-product-data' => [
                                        'title' => 'Haipo Bidhaa Data',
                                        'body'  => 'Haiwezekani hadi process imechaguliwa bidhaa.',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'other-information' => [
                'title' => 'Nyingine Taarifa',

                'fieldset' => [
                    'sales' => [
                        'title' => 'Mauzo',

                        'fields' => [
                            'sales-person'       => 'Mauzo Person',
                            'customer-reference' => 'Kumbukumbu ya mteja',
                            'tags'               => 'Lebo',
                        ],
                    ],

                    'shipping' => [
                        'title'  => 'Shipping',
                        'fields' => [
                            'warehouse'       => 'Ghala',
                            'commitment-date' => 'Usafirishaji Tarehe',
                        ],
                    ],

                    'tracking' => [
                        'title'  => 'Tracking',
                        'fields' => [
                            'source-document' => 'Chanzo Hati',
                            'medium'          => 'Wastani',
                            'source'          => 'Chanzo',
                            'campaign'        => 'Campaign',
                        ],
                    ],

                    'additional-information' => [
                        'title' => 'Additional Taarifa',

                        'fields' => [
                            'company'  => 'Kampuni',
                            'currency' => 'Sarafu',
                        ],
                    ],
                ],
            ],

            'term-and-conditions' => [
                'title' => 'Terms & Conditions',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'number'             => 'Nambari',
            'status'             => 'Hali',
            'delivery-status'    => 'Usafirishaji Hali',
            'invoice-status'     => 'Ankara Hali',
            'order-date'         => 'Agizo Tarehe',
            'creation-date'      => 'Creation Tarehe',
            'commitment-date'    => 'Commitment Tarehe',
            'expected-date'      => 'Inatarajiwa Tarehe',
            'customer'           => 'Mteja',
            'sales-person'       => 'Mauzo Person',
            'sales-team'         => 'Mauzo Timu',
            'untaxed-amount'     => 'Bila kodi Kiasi',
            'amount-tax'         => 'Kiasi Kodi',
            'amount-total'       => 'Kiasi Jumla',
            'customer-reference' => 'Kumbukumbu ya mteja',
        ],

        'summarizers' => [
            'total'        => 'Jumla',
            'taxes'        => 'Kodi',
            'total-amount' => 'Jumla Kiasi',
        ],

        'filters' => [
            'sales-person'     => 'Mauzo Person',
            'utm-source'       => 'UTM Chanzo',
            'company'          => 'Kampuni',
            'customer'         => 'Mteja',
            'journal'          => 'Jarida',
            'invoice-address'  => 'Ankara Anwani',
            'shipping-address' => 'Shipping Anwani',
            'fiscal-position'  => 'Kifedha Cheo',
            'payment-term'     => 'Masharti ya malipo',
            'currency'         => 'Sarafu',
            'created-at'       => 'Imeundwa',
            'updated-at'       => 'Imesasishwa',
        ],

        'groups' => [
            'medium'          => 'Wastani',
            'source'          => 'Chanzo',
            'team'            => 'Timu',
            'sales-person'    => 'Mauzo Person',
            'currency'        => 'Sarafu',
            'company'         => 'Kampuni',
            'customer'        => 'Mteja',
            'quotation-date'  => 'Nukuu Tarehe',
            'commitment-date' => 'Commitment Tarehe',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Nukuu restored',
                    'body'  => 'nukuu ina restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Nukuu imefutwa',
                    'body'  => 'nukuu ina imefutwa successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Nukuu force imefutwa',
                    'body'  => 'nukuu ina force imefutwa successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Nukuu restored',
                    'body'  => 'nukuu ina restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Nukuu imefutwa',
                    'body'  => 'nukuu ina imefutwa successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Nukuu force imefutwa',
                    'body'  => 'nukuu ina force imefutwa successfully.',
                ],
            ],
        ],

        'empty-state-action' => [
            'create' => [
                'notification' => [
                    'title' => 'Nukuu imeundwa',
                    'body'  => 'nukuu ina imeundwa successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'section' => [
            'general' => [
                'title'   => 'Jumla',
                'entries' => [
                    'sale-order'     => 'Agizo la mauzo',
                    'customer'       => 'Mteja',
                    'expiration'     => 'Expiration',
                    'quotation-date' => 'Nukuu Tarehe',
                    'payment-term'   => 'Masharti ya malipo',
                ],
            ],
        ],

        'tabs' => [
            'order-line' => [
                'title' => 'Agizo Mstari',

                'repeater' => [
                    'products' => [
                        'title'       => 'Bidhaa',
                        'add-product' => 'Ongeza bidhaa',
                        'entries'     => [
                            'product'             => 'Bidhaa',
                            'product-variants'    => 'Bidhaa Variants',
                            'product-simple'      => 'Bidhaa Simple',
                            'quantity'            => 'Kiasi',
                            'qty-delivered'       => 'Delivered',
                            'qty-invoiced'        => 'Invoiced',
                            'uom'                 => 'UOM',
                            'lead-time'           => 'Lead Time',
                            'packaging-qty'       => 'Kifungashio Kiasi',
                            'packaging'           => 'Kifungashio',
                            'unit-price'          => 'Bei ya uniti',
                            'cost'                => 'Gharama',
                            'margin'              => 'Pembe',
                            'taxes'               => 'Kodi',
                            'amount'              => 'Kiasi',
                            'margin-percentage'   => 'Pembe (%)',
                            'discount-percentage' => 'Punguzo (%)',
                            'sub-total'           => 'Jumla ndogo',
                        ],
                    ],

                    'product-optional' => [
                        'title'       => 'Si lazima Bidhaa',
                        'add-product' => 'Ongeza bidhaa',
                        'entries'     => [
                            'product'             => 'Bidhaa',
                            'description'         => 'Maelezo',
                            'quantity'            => 'Kiasi',
                            'uom'                 => 'Kipimo',
                            'unit-price'          => 'Bei ya uniti',
                            'discount-percentage' => 'Punguzo (%)',
                            'sub-total'           => 'Jumla ndogo',

                            'actions' => [
                                'tooltip' => [
                                    'add-order-line' => 'Ongeza Agizo Mstari',
                                ],

                                'notifications' => [
                                    'product-added' => [
                                        'title' => 'Bidhaa added',
                                        'body'  => 'bidhaa ina added successfully.',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'other-information' => [
                'title' => 'Nyingine Taarifa',

                'fieldset' => [
                    'sales' => [
                        'title' => 'Mauzo',

                        'entries' => [
                            'sales-person'       => 'Mauzo Person',
                            'customer-reference' => 'Kumbukumbu ya mteja',
                            'tags'               => 'Lebo',
                        ],
                    ],

                    'shipping' => [
                        'title'   => 'Shipping',
                        'entries' => [
                            'commitment-date' => 'Usafirishaji Tarehe',
                        ],
                    ],

                    'tracking' => [
                        'title'   => 'Tracking',
                        'entries' => [
                            'source-document' => 'Chanzo Hati',
                            'medium'          => 'Wastani',
                            'source'          => 'Chanzo',
                            'campaign'        => 'Campaign',
                        ],
                    ],

                    'additional-information' => [
                        'title' => 'Additional Taarifa',

                        'entries' => [
                            'company'  => 'Kampuni',
                            'currency' => 'Sarafu',
                        ],
                    ],
                ],
            ],

            'term-and-conditions' => [
                'title' => 'Terms & Conditions',
            ],
        ],
    ],
];
