<?php

return [
    'title' => 'Nukuu ya bei',

    'navigation' => [
        'title' => 'Nukuu za bei',
    ],

    'global-search' => [
        'customer'  => 'Mteja',
        'reference' => 'Rejea',
        'amount'    => 'Kiasi',
    ],

    'form' => [
        'section' => [
            'general' => [
                'title'  => 'Jumla',
                'fields' => [
                    'customer'       => 'Mteja',
                    'expiration'     => 'Mwisho wa uhalali',
                    'quotation-date' => 'Tarehe ya nukuu',
                    'order-date'     => 'Tarehe ya oda',
                    'payment-term'   => 'Masharti ya malipo',
                ],
            ],
        ],

        'tabs' => [
            'order-line' => [
                'title' => 'Mistari ya oda',

                'repeater' => [
                    'products' => [
                        'title'       => 'Bidhaa',
                        'add-product' => 'Ongeza bidhaa',

                        'columns'     => [
                            'product'                    => 'Bidhaa',
                            'product-variants'           => 'Aina za bidhaa',
                            'product-simple'             => 'Bidhaa rahisi',
                            'quantity'                   => 'Idadi',
                            'insufficient-stock-tooltip' => 'Hisa hazitoshi kukidhi mahitaji haya.',
                            'uom'                        => 'Kipimo',
                            'lead-time'                  => 'Muda wa kufikisha',
                            'qty-delivered'              => 'Imewasilishwa',
                            'qty-invoiced'               => 'Imetolewa ankara',
                            'packaging-qty'              => 'Idadi ya vifurushi',
                            'packaging'                  => 'Kifurushi',
                            'unit-price'                 => 'Bei ya kipimo',
                            'cost'                       => 'Gharama',
                            'margin'                     => 'Faida',
                            'taxes'                      => 'Kodi',
                            'amount'                     => 'Kiasi',
                            'margin-percentage'          => 'Faida (%)',
                            'discount-percentage'        => 'Punguzo (%)',
                        ],

                        'fields'      => [
                            'product'             => 'Bidhaa',
                            'product-variants'    => 'Aina za bidhaa',
                            'product-simple'      => 'Bidhaa rahisi',
                            'quantity'            => 'Idadi',
                            'uom'                 => 'Kipimo',
                            'lead-time'           => 'Muda wa kufikisha',
                            'qty-delivered'       => 'Idadi iliyowasilishwa',
                            'qty-invoiced'        => 'Idadi iliyotolewa ankara',
                            'packaging-qty'       => 'Idadi ya vifurushi',
                            'packaging'           => 'Kifurushi',
                            'unit-price'          => 'Bei ya kipimo',
                            'cost'                => 'Gharama',
                            'margin'              => 'Faida',
                            'taxes'               => 'Kodi',
                            'amount'              => 'Kiasi',
                            'margin-percentage'   => 'Faida (%)',
                            'discount-percentage' => 'Punguzo (%)',
                        ],

                        'notifications' => [
                            'quantity-below-delivered' => [
                                'title' => 'Haiwezekani kupunguza idadi',
                                'body'  => 'Huwezi kupunguza idadi chini ya idadi iliyowasilishwa (:qty).',
                            ],
                        ],

                        'delete-action' => [
                            'error' => [
                                'title' => 'Haiwezekani kufuta bidhaa',
                                'body'  => 'Bidhaa haziwezi kufutwa kwenye oda ya mauzo iliyothibitishwa.',
                            ],
                        ],

                        'actions' => [
                            'open-product' => [
                                'tooltip' => 'Fungua bidhaa',
                            ],
                        ],
                    ],

                    'product-optional' => [
                        'title'       => 'Bidhaa za hiari',
                        'add-product' => 'Ongeza bidhaa',

                        'columns' => [
                            'product'             => 'Bidhaa',
                            'description'         => 'Maelezo',
                            'quantity'            => 'Idadi',
                            'uom'                 => 'Kipimo',
                            'unit-price'          => 'Bei ya kipimo',
                            'discount-percentage' => 'Punguzo (%)',
                        ],

                        'fields'      => [
                            'product'             => 'Bidhaa',
                            'description'         => 'Maelezo',
                            'quantity'            => 'Idadi',
                            'uom'                 => 'Kipimo',
                            'unit-price'          => 'Bei ya kipimo',
                            'discount-percentage' => 'Punguzo (%)',

                            'actions' => [
                                'tooltip' => [
                                    'add-order-line' => 'Ongeza mstari wa oda',
                                    'already-added'  => 'Tayari iko kwenye oda',
                                ],

                                'notifications' => [
                                    'product-added' => [
                                        'title' => 'Bidhaa imeongezwa',
                                        'body'  => 'Bidhaa imeongezwa kwa mafanikio.',
                                    ],

                                    'product-not-found' => [
                                        'title' => 'Bidhaa haikupatikana',
                                    ],

                                    'product-already-exists' => [
                                        'title' => 'Bidhaa tayari ipo',
                                        'body'  => 'Bidhaa hii tayari iko kwenye mistari ya oda. Tafadhali sasisha mstari uliopo badala yake.',
                                    ],

                                    'missing-product-data' => [
                                        'title' => 'Taarifa za bidhaa hazipo',
                                        'body'  => 'Imeshindikana kuchakata bidhaa iliyochaguliwa.',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'other-information' => [
                'title' => 'Taarifa nyingine',

                'fieldset' => [
                    'sales' => [
                        'title' => 'Mauzo',

                        'fields' => [
                            'sales-person'       => 'Muuzaji',
                            'customer-reference' => 'Rejea ya mteja',
                            'tags'               => 'Lebo',
                        ],
                    ],

                    'shipping' => [
                        'title'  => 'Usafirishaji',
                        'fields' => [
                            'warehouse'       => 'Ghala',
                            'commitment-date' => 'Tarehe ya uwasilishaji',
                        ],
                    ],

                    'tracking' => [
                        'title'  => 'Ufuatiliaji',
                        'fields' => [
                            'source-document' => 'Hati chanzo',
                            'medium'          => 'Njia',
                            'source'          => 'Chanzo',
                            'campaign'        => 'Kampeni',
                        ],
                    ],

                    'additional-information' => [
                        'title' => 'Taarifa za ziada',

                        'fields' => [
                            'company'  => 'Kampuni',
                            'currency' => 'Sarafu',
                        ],
                    ],
                ],
            ],

            'term-and-conditions' => [
                'title' => 'Vigezo na masharti',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'number'             => 'Namba',
            'status'             => 'Hali',
            'delivery-status'    => 'Hali ya uwasilishaji',
            'invoice-status'     => 'Hali ya ankara',
            'order-date'         => 'Tarehe ya oda',
            'creation-date'      => 'Tarehe ya kuundwa',
            'commitment-date'    => 'Tarehe iliyoahidiwa',
            'expected-date'      => 'Tarehe inayotarajiwa',
            'customer'           => 'Mteja',
            'sales-person'       => 'Muuzaji',
            'sales-team'         => 'Timu ya mauzo',
            'untaxed-amount'     => 'Kiasi bila kodi',
            'amount-tax'         => 'Kiasi cha kodi',
            'amount-total'       => 'Jumla ya kiasi',
            'customer-reference' => 'Rejea ya mteja',
        ],

        'summarizers' => [
            'total'        => 'Jumla',
            'taxes'        => 'Kodi',
            'total-amount' => 'Jumla ya kiasi',
        ],

        'filters' => [
            'sales-person'     => 'Muuzaji',
            'utm-source'       => 'Chanzo cha UTM',
            'company'          => 'Kampuni',
            'customer'         => 'Mteja',
            'journal'          => 'Jarida',
            'invoice-address'  => 'Anwani ya ankara',
            'shipping-address' => 'Anwani ya usafirishaji',
            'fiscal-position'  => 'Hali ya kodi',
            'payment-term'     => 'Masharti ya malipo',
            'currency'         => 'Sarafu',
            'created-at'       => 'Iliundwa',
            'updated-at'       => 'Ilisasishwa',
        ],

        'groups' => [
            'medium'          => 'Njia',
            'source'          => 'Chanzo',
            'team'            => 'Timu',
            'sales-person'    => 'Muuzaji',
            'currency'        => 'Sarafu',
            'company'         => 'Kampuni',
            'customer'        => 'Mteja',
            'quotation-date'  => 'Tarehe ya nukuu',
            'commitment-date' => 'Tarehe iliyoahidiwa',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Nukuu imerejeshwa',
                    'body'  => 'Nukuu imerejeshwa kwa mafanikio.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Nukuu imefutwa',
                    'body'  => 'Nukuu imefutwa kwa mafanikio.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Nukuu imefutwa kabisa',
                    'body'  => 'Nukuu imefutwa kabisa kwa mafanikio.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Nukuu zimerejeshwa',
                    'body'  => 'Nukuu zimerejeshwa kwa mafanikio.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Nukuu zimefutwa',
                    'body'  => 'Nukuu zimefutwa kwa mafanikio.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Nukuu zimefutwa kabisa',
                    'body'  => 'Nukuu zimefutwa kabisa kwa mafanikio.',
                ],
            ],
        ],

        'empty-state-action' => [
            'create' => [
                'notification' => [
                    'title' => 'Nukuu zimeundwa',
                    'body'  => 'Nukuu zimeundwa kwa mafanikio.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'section' => [
            'general' => [
                'title'   => 'Jumla',
                'entries' => [
                    'sale-order'     => 'Oda ya mauzo',
                    'customer'       => 'Mteja',
                    'expiration'     => 'Mwisho wa uhalali',
                    'quotation-date' => 'Tarehe ya nukuu',
                    'payment-term'   => 'Masharti ya malipo',
                ],
            ],
        ],

        'tabs' => [
            'order-line' => [
                'title' => 'Mistari ya oda',

                'repeater' => [
                    'products' => [
                        'title'       => 'Bidhaa',
                        'add-product' => 'Ongeza bidhaa',
                        'entries'     => [
                            'product'             => 'Bidhaa',
                            'product-variants'    => 'Aina za bidhaa',
                            'product-simple'      => 'Bidhaa rahisi',
                            'quantity'            => 'Idadi',
                            'qty-delivered'       => 'Imewasilishwa',
                            'qty-invoiced'        => 'Imetolewa ankara',
                            'uom'                 => 'Kipimo',
                            'lead-time'           => 'Muda wa kufikisha',
                            'packaging-qty'       => 'Idadi ya vifurushi',
                            'packaging'           => 'Kifurushi',
                            'unit-price'          => 'Bei ya kipimo',
                            'cost'                => 'Gharama',
                            'margin'              => 'Faida',
                            'taxes'               => 'Kodi',
                            'amount'              => 'Kiasi',
                            'margin-percentage'   => 'Faida (%)',
                            'discount-percentage' => 'Punguzo (%)',
                            'sub-total'           => 'Jumla ndogo',
                        ],
                    ],

                    'product-optional' => [
                        'title'       => 'Bidhaa za hiari',
                        'add-product' => 'Ongeza bidhaa',
                        'entries'     => [
                            'product'             => 'Bidhaa',
                            'description'         => 'Maelezo',
                            'quantity'            => 'Idadi',
                            'uom'                 => 'Kipimo',
                            'unit-price'          => 'Bei ya kipimo',
                            'discount-percentage' => 'Punguzo (%)',
                            'sub-total'           => 'Jumla ndogo',

                            'actions' => [
                                'tooltip' => [
                                    'add-order-line' => 'Ongeza mstari wa oda',
                                ],

                                'notifications' => [
                                    'product-added' => [
                                        'title' => 'Bidhaa imeongezwa',
                                        'body'  => 'Bidhaa imeongezwa kwa mafanikio.',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'other-information' => [
                'title' => 'Taarifa nyingine',

                'fieldset' => [
                    'sales' => [
                        'title' => 'Mauzo',

                        'entries' => [
                            'sales-person'       => 'Muuzaji',
                            'customer-reference' => 'Rejea ya mteja',
                            'tags'               => 'Lebo',
                        ],
                    ],

                    'shipping' => [
                        'title'   => 'Usafirishaji',
                        'entries' => [
                            'commitment-date' => 'Tarehe ya uwasilishaji',
                        ],
                    ],

                    'tracking' => [
                        'title'   => 'Ufuatiliaji',
                        'entries' => [
                            'source-document' => 'Hati chanzo',
                            'medium'          => 'Njia',
                            'source'          => 'Chanzo',
                            'campaign'        => 'Kampeni',
                        ],
                    ],

                    'additional-information' => [
                        'title' => 'Taarifa za ziada',

                        'entries' => [
                            'company'  => 'Kampuni',
                            'currency' => 'Sarafu',
                        ],
                    ],
                ],
            ],

            'term-and-conditions' => [
                'title' => 'Vigezo na masharti',
            ],
        ],
    ],
];
