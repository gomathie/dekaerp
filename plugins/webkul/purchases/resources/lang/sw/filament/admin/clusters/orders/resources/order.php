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
                    'vendor-reference'         => 'Kumbukumbu ya msambazaji',
                    'vendor-reference-tooltip' => 'Nambari ya kumbukumbu ya oda ya mauzo au zabuni iliyotolewa na msambazaji. Hutumika kulinganisha wakati wa kupokea bidhaa, kwa kuwa kwa kawaida huwa kwenye hati ya uwasilishaji ya msambazaji.',
                    'agreement'                => 'Makubaliano',
                    'currency'                 => 'Sarafu',
                    'confirmation-date'        => 'Tarehe ya uthibitisho',
                    'order-deadline'           => 'Tarehe ya mwisho ya oda',
                    'expected-arrival'         => 'Tarehe ya kuwasili inayotarajiwa',
                    'confirmed-by-vendor'      => 'Imethibitishwa na msambazaji',
                    'deliver-to'               => 'Wasilisha kwa',
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
                            'expected-arrival'    => 'Tarehe ya kuwasili inayotarajiwa',
                            'quantity'            => 'Idadi',
                            'received'            => 'Imepokelewa',
                            'billed'              => 'Imetozwa',
                            'unit'                => 'Kipimo',
                            'packaging-qty'       => 'Idadi ya vifungashio',
                            'packaging'           => 'Kifungashio',
                            'taxes'               => 'Kodi',
                            'discount-percentage' => 'Punguzo (%)',
                            'unit-price'          => 'Bei ya kipimo',
                            'amount'              => 'Kiasi',
                        ],

                        'notifications' => [
                            'quantity-below-received' => [
                                'title' => 'Huwezi kupunguza idadi',
                                'body'  => 'Huwezi kupunguza idadi chini ya idadi iliyopokelewa (:qty).',
                            ],

                            'blanket-order-qty-limit' => [
                                'title' => 'Idadi inazidi kikomo cha oda ya jumla',
                                'body'  => 'Idadi ya bidhaa (:product_qty) inazidi idadi inayopatikana (:available_qty) kwenye oda ya jumla.',
                            ],
                        ],

                        'columns' => [
                            'product'             => 'Bidhaa',
                            'expected-arrival'    => 'Tarehe ya kuwasili inayotarajiwa',
                            'quantity'            => 'Idadi',
                            'received'            => 'Imepokelewa',
                            'billed'              => 'Imetozwa',
                            'unit'                => 'Kipimo',
                            'packaging-qty'       => 'Idadi ya vifungashio',
                            'packaging'           => 'Kifungashio',
                            'taxes'               => 'Kodi',
                            'discount-percentage' => 'Punguzo (%)',
                            'unit-price'          => 'Bei ya kipimo',
                            'amount'              => 'Kiasi',
                        ],

                        'delete-action' => [
                            'error' => [
                                'title' => 'Huwezi kufuta bidhaa',
                                'body'  => 'Bidhaa haziwezi kufutwa kwenye oda ya ununuzi iliyothibitishwa.',
                            ],
                        ],

                        'actions' => [
                            'open-product' => [
                                'tooltip' => 'Fungua bidhaa',
                            ],
                        ],
                    ],

                    'section' => [
                        'title' => 'Ongeza sehemu',

                        'fields' => [],
                    ],

                    'note' => [
                        'title' => 'Ongeza kidokezo',

                        'fields' => [],
                    ],
                ],
            ],

            'additional' => [
                'title' => 'Taarifa za ziada',

                'fields' => [
                    'buyer'             => 'Mnunuzi',
                    'company'           => 'Kampuni',
                    'source-document'   => 'Hati chanzo',
                    'incoterm'          => 'Incoterm',
                    'incoterm-tooltip'  => 'Masharti ya Kimataifa ya Biashara (Incoterms) ni seti ya masharti sanifu ya biashara yanayotumika katika miamala ya kimataifa kufafanua majukumu ya wanunuzi na wauzaji.',
                    'incoterm-location' => 'Mahali pa Incoterm',
                    'payment-term'      => 'Masharti ya malipo',
                    'fiscal-position'   => 'Hali ya kodi',
                ],
            ],

            'terms' => [
                'title' => 'Sheria na masharti',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'favorite'         => 'Kipendwa',
            'priority'         => 'Kipaumbele',
            'vendor-reference' => 'Kumbukumbu ya msambazaji',
            'reference'        => 'Kumbukumbu',
            'vendor'           => 'Msambazaji',
            'buyer'            => 'Mnunuzi',
            'company'          => 'Kampuni',
            'order-deadline'   => 'Tarehe ya mwisho ya oda',
            'source-document'  => 'Hati chanzo',
            'untaxed-amount'   => 'Kiasi bila kodi',
            'total-amount'     => 'Jumla ya kiasi',
            'status'           => 'Hali',
            'billing-status'   => 'Hali ya bili',
            'receipt-status'   => 'Hali ya upokeaji',
            'currency'         => 'Sarafu',
        ],

        'groups' => [
            'vendor'     => 'Msambazaji',
            'buyer'      => 'Mnunuzi',
            'state'      => 'Hali',
            'created-at' => 'Iliundwa',
            'updated-at' => 'Ilisasishwa',
        ],

        'filters' => [
            'status'           => 'Hali',
            'vendor-reference' => 'Kumbukumbu ya msambazaji',
            'reference'        => 'Kumbukumbu',
            'untaxed-amount'   => 'Kiasi bila kodi',
            'total-amount'     => 'Jumla ya kiasi',
            'order-deadline'   => 'Tarehe ya mwisho ya oda',
            'vendor'           => 'Msambazaji',
            'buyer'            => 'Mnunuzi',
            'company'          => 'Kampuni',
            'payment-term'     => 'Masharti ya malipo',
            'incoterm'         => 'Incoterm',
            'status'           => 'Hali',
            'created-at'       => 'Iliundwa',
            'updated-at'       => 'Ilisasishwa',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Oda imefutwa',
                        'body'  => 'Oda imefutwa.',
                    ],

                    'error' => [
                        'title' => 'Oda haikuweza kufutwa',
                        'body'  => 'Oda haiwezi kufutwa kwa sababu inatumika.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Oda zimefutwa',
                        'body'  => 'Oda zimefutwa.',
                    ],

                    'error' => [
                        'title' => 'Oda hazikuweza kufutwa',
                        'body'  => 'Oda haziwezi kufutwa kwa sababu zinatumika.',
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
                    'purchase-order'           => 'Oda ya ununuzi',
                    'vendor'                   => 'Msambazaji',
                    'vendor-reference'         => 'Kumbukumbu ya msambazaji',
                    'vendor-reference-tooltip' => 'Nambari ya kumbukumbu ya oda ya mauzo au zabuni iliyotolewa na msambazaji. Hutumika kulinganisha wakati wa kupokea bidhaa, kwa kuwa kwa kawaida huwa kwenye hati ya uwasilishaji ya msambazaji.',
                    'agreement'                => 'Makubaliano',
                    'currency'                 => 'Sarafu',
                    'confirmation-date'        => 'Tarehe ya uthibitisho',
                    'order-deadline'           => 'Tarehe ya mwisho ya oda',
                    'expected-arrival'         => 'Tarehe ya kuwasili inayotarajiwa',
                    'confirmed-by-vendor'      => 'Imethibitishwa na msambazaji',
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
                            'expected-arrival'    => 'Tarehe ya kuwasili inayotarajiwa',
                            'quantity'            => 'Idadi',
                            'received'            => 'Imepokelewa',
                            'billed'              => 'Imetozwa',
                            'unit'                => 'Kipimo',
                            'packaging-qty'       => 'Idadi ya vifungashio',
                            'packaging'           => 'Kifungashio',
                            'taxes'               => 'Kodi',
                            'discount-percentage' => 'Punguzo (%)',
                            'unit-price'          => 'Bei ya kipimo',
                            'amount'              => 'Kiasi',
                        ],
                    ],

                    'section' => [
                        'title' => 'Ongeza sehemu',
                    ],

                    'note' => [
                        'title' => 'Ongeza kidokezo',
                    ],
                ],
            ],

            'additional' => [
                'title' => 'Taarifa za ziada',

                'entries' => [
                    'buyer'             => 'Mnunuzi',
                    'company'           => 'Kampuni',
                    'source-document'   => 'Hati chanzo',
                    'incoterm'          => 'Incoterm',
                    'incoterm-tooltip'  => 'Masharti ya Kimataifa ya Biashara (Incoterms) ni seti ya masharti sanifu ya biashara yanayotumika katika miamala ya kimataifa kufafanua majukumu ya wanunuzi na wauzaji.',
                    'incoterm-location' => 'Mahali pa Incoterm',
                    'payment-term'      => 'Masharti ya malipo',
                    'fiscal-position'   => 'Hali ya kodi',
                ],
            ],

            'terms' => [
                'title' => 'Sheria na masharti',
            ],
        ],
    ],
];
