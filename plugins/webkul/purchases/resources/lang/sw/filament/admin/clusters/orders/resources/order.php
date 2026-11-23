<?php

return [
    'global-search' => [
        'vendor'    => 'Msambazaji',
        'reference' => 'Kumbukumbu',
        'amount'    => 'Kiasi',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Jumla',

                'fields' => [
                    'vendor'                   => 'Msambazaji',
                    'vendor-reference'         => 'Msambazaji Kumbukumbu',
                    'vendor-reference-tooltip' => 'kumbukumbu nambari ya agizo la mauzo au bid provided na msambazaji. Hii ni inatumika kwa matching wakati receiving bidhaa, kama hii kumbukumbu ni typically imejumuishwa katika vendor\'s usafirishaji agizo.',
                    'agreement'                => 'Agreement',
                    'currency'                 => 'Sarafu',
                    'confirmation-date'        => 'Confirmation Tarehe',
                    'order-deadline'           => 'Agizo Deadline',
                    'expected-arrival'         => 'Inatarajiwa Arrival',
                    'confirmed-by-vendor'      => 'Imethibitishwa na Msambazaji',
                    'deliver-to'               => 'Deliver Hadi',
                ],
            ],
        ],

        'tabs' => [
            'products' => [
                'title' => 'Bidhaa',

                'repeater' => [
                    'products' => [
                        'title'            => 'Bidhaa',
                        'add-product-line' => 'Ongeza bidhaa',

                        'fields' => [
                            'product'             => 'Bidhaa',
                            'expected-arrival'    => 'Inatarajiwa Arrival',
                            'quantity'            => 'Kiasi',
                            'received'            => 'Imepokelewa',
                            'billed'              => 'Billed',
                            'unit'                => 'Uniti',
                            'packaging-qty'       => 'Kifungashio Qty',
                            'packaging'           => 'Kifungashio',
                            'taxes'               => 'Kodi',
                            'discount-percentage' => 'Punguzo (%)',
                            'unit-price'          => 'Bei ya uniti',
                            'amount'              => 'Kiasi',
                        ],

                        'notifications' => [
                            'quantity-below-received' => [
                                'title' => 'Haiwezekani Reduce Kiasi',
                                'body'  => 'Wewe haiwezekani reduce kiasi chini ya imepokelewa kiasi (:qty).',
                            ],

                            'blanket-order-qty-limit' => [
                                'title' => 'Kiasi Exceeds Blanket Agizo Limit',
                                'body'  => 'bidhaa kiasi (:product_qty) exceeds inapatikana kiasi (:available_qty) kutoka blanket agizo.',
                            ],
                        ],

                        'columns' => [
                            'product'             => 'Bidhaa',
                            'expected-arrival'    => 'Inatarajiwa Arrival',
                            'quantity'            => 'Kiasi',
                            'received'            => 'Imepokelewa',
                            'billed'              => 'Billed',
                            'unit'                => 'Uniti',
                            'packaging-qty'       => 'Kifungashio Qty',
                            'packaging'           => 'Kifungashio',
                            'taxes'               => 'Kodi',
                            'discount-percentage' => 'Punguzo (%)',
                            'unit-price'          => 'Bei ya uniti',
                            'amount'              => 'Kiasi',
                        ],

                        'delete-action' => [
                            'error' => [
                                'title' => 'Haiwezekani Futa Bidhaa',
                                'body'  => 'Bidhaa haiwezekani kuwa imefutwa kutoka imethibitishwa agizo la ununuzi.',
                            ],
                        ],

                        'actions' => [
                            'open-product' => [
                                'tooltip' => 'Wazi bidhaa',
                            ],
                        ],
                    ],

                    'section' => [
                        'title' => 'Ongeza Sehemu',

                        'fields' => [],
                    ],

                    'note' => [
                        'title' => 'Ongeza Maelezo',

                        'fields' => [],
                    ],
                ],
            ],

            'additional' => [
                'title' => 'Additional Taarifa',

                'fields' => [
                    'buyer'             => 'Buyer',
                    'company'           => 'Kampuni',
                    'source-document'   => 'Chanzo Hati',
                    'incoterm'          => 'Incoterm',
                    'incoterm-tooltip'  => 'International Commercial Terms (Incoterms) ni weka ya standardized trade terms inatumika katika global transactions hadi define responsibilities kati ya buyers na sellers.',
                    'incoterm-location' => 'Incoterm Mahali',
                    'payment-term'      => 'Masharti ya malipo',
                    'fiscal-position'   => 'Kifedha Cheo',
                ],
            ],

            'terms' => [
                'title' => 'Terms na Conditions',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'favorite'         => 'Favorite',
            'priority'         => 'Kipaumbele',
            'vendor-reference' => 'Msambazaji Kumbukumbu',
            'reference'        => 'Kumbukumbu',
            'vendor'           => 'Msambazaji',
            'buyer'            => 'Buyer',
            'company'          => 'Kampuni',
            'order-deadline'   => 'Agizo Deadline',
            'source-document'  => 'Chanzo Hati',
            'untaxed-amount'   => 'Bila kodi Kiasi',
            'total-amount'     => 'Jumla Kiasi',
            'status'           => 'Hali',
            'billing-status'   => 'Billing Hali',
            'receipt-status'   => 'Stakabadhi Hali',
            'currency'         => 'Sarafu',
        ],

        'groups' => [
            'vendor'     => 'Msambazaji',
            'buyer'      => 'Buyer',
            'state'      => 'Hali',
            'created-at' => 'Imeundwa',
            'updated-at' => 'Imesasishwa',
        ],

        'filters' => [
            'status'           => 'Hali',
            'vendor-reference' => 'Msambazaji Kumbukumbu',
            'reference'        => 'Kumbukumbu',
            'untaxed-amount'   => 'Bila kodi Kiasi',
            'total-amount'     => 'Jumla Kiasi',
            'order-deadline'   => 'Agizo Deadline',
            'vendor'           => 'Msambazaji',
            'buyer'            => 'Buyer',
            'company'          => 'Kampuni',
            'payment-term'     => 'Masharti ya malipo',
            'incoterm'         => 'Incoterm',
            'status'           => 'Hali',
            'created-at'       => 'Imeundwa',
            'updated-at'       => 'Imesasishwa',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Agizo imefutwa',
                        'body'  => 'agizo ina imefutwa successfully.',
                    ],

                    'error' => [
                        'title' => 'Agizo could si kuwa imefutwa',
                        'body'  => 'agizo haiwezekani kuwa imefutwa kwa sababu hii ni currently katika tumia.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Maagizo imefutwa',
                        'body'  => 'maagizo ina imefutwa successfully.',
                    ],

                    'error' => [
                        'title' => 'Maagizo could si kuwa imefutwa',
                        'body'  => 'maagizo haiwezekani kuwa imefutwa kwa sababu wao ni currently katika tumia.',
                    ],
                ],
            ],
        ],
    ],

    'summary' => [
        'tax' => 'Kodi',
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Jumla',

                'entries' => [
                    'purchase-order'           => 'Agizo la ununuzi',
                    'vendor'                   => 'Msambazaji',
                    'vendor-reference'         => 'Msambazaji Kumbukumbu',
                    'vendor-reference-tooltip' => 'kumbukumbu nambari ya agizo la mauzo au bid provided na msambazaji. Hii ni inatumika kwa matching wakati receiving bidhaa, kama hii kumbukumbu ni typically imejumuishwa katika vendor\'s usafirishaji agizo.',
                    'agreement'                => 'Agreement',
                    'currency'                 => 'Sarafu',
                    'confirmation-date'        => 'Confirmation Tarehe',
                    'order-deadline'           => 'Agizo Deadline',
                    'expected-arrival'         => 'Inatarajiwa Arrival',
                    'confirmed-by-vendor'      => 'Imethibitishwa na Msambazaji',
                ],
            ],
        ],

        'tabs' => [
            'products' => [
                'title' => 'Bidhaa',

                'repeater' => [
                    'products' => [
                        'title'            => 'Bidhaa',
                        'add-product-line' => 'Ongeza bidhaa',

                        'entries' => [
                            'product'             => 'Bidhaa',
                            'expected-arrival'    => 'Inatarajiwa Arrival',
                            'quantity'            => 'Kiasi',
                            'received'            => 'Imepokelewa',
                            'billed'              => 'Billed',
                            'unit'                => 'Uniti',
                            'packaging-qty'       => 'Kifungashio Qty',
                            'packaging'           => 'Kifungashio',
                            'taxes'               => 'Kodi',
                            'discount-percentage' => 'Punguzo (%)',
                            'unit-price'          => 'Bei ya uniti',
                            'amount'              => 'Kiasi',
                        ],
                    ],

                    'section' => [
                        'title' => 'Ongeza Sehemu',
                    ],

                    'note' => [
                        'title' => 'Ongeza Maelezo',
                    ],
                ],
            ],

            'additional' => [
                'title' => 'Additional Taarifa',

                'entries' => [
                    'buyer'             => 'Buyer',
                    'company'           => 'Kampuni',
                    'source-document'   => 'Chanzo Hati',
                    'incoterm'          => 'Incoterm',
                    'incoterm-tooltip'  => 'International Commercial Terms (Incoterms) ni weka ya standardized trade terms inatumika katika global transactions hadi define responsibilities kati ya buyers na sellers.',
                    'incoterm-location' => 'Incoterm Mahali',
                    'payment-term'      => 'Masharti ya malipo',
                    'fiscal-position'   => 'Kifedha Cheo',
                ],
            ],

            'terms' => [
                'title' => 'Terms na Conditions',
            ],
        ],
    ],
];
