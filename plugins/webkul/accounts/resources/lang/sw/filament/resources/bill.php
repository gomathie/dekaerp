<?php

return [
    'title' => 'Bili',

    'navigation' => [
        'title' => 'Bili',
        'group' => 'Ankara',
    ],

    'global-search' => [
        'vendor'   => 'Msambazaji',
        'date'     => 'Tarehe',
        'due-date' => 'Tarehe ya mwisho',
    ],

    'form' => [
        'section' => [
            'general' => [
                'title'  => 'Taarifa za jumla',
                'fields' => [
                    'vendor-bill'       => 'Bili ya msambazaji',
                    'vendor'            => 'Msambazaji',
                    'bill-date'         => 'Tarehe ya bili',
                    'bill-reference'    => 'Rejea ya bili',
                    'accounting-date'   => 'Tarehe ya uhasibu',
                    'payment-reference' => 'Rejea ya malipo',
                    'recipient-bank'    => 'Benki ya mpokeaji',
                    'due-date'          => 'Tarehe ya mwisho',
                    'payment-term'      => 'Sharti la malipo',
                    'journal'           => 'Jarida',
                    'currency'          => 'Sarafu',
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

                        'columns' => [
                            'product'             => 'Bidhaa / huduma',
                            'quantity'            => 'Idadi',
                            'unit'                => 'Kipimo',
                            'taxes'               => 'Kodi',
                            'discount-percentage' => 'Asilimia ya punguzo',
                            'unit-price'          => 'Bei ya kipimo',
                            'sub-total'           => 'Jumla ndogo',
                        ],

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
                    'accounting' => [
                        'title' => 'Uhasibu',

                        'fields' => [
                            'company'                 => 'Kampuni',
                            'incoterm'                => 'Incoterm',
                            'incoterm-location'       => 'Mahali pa Incoterm',
                            'payment-method'          => 'Njia ya malipo',
                            'fiscal-position'         => 'Nafasi ya kodi',
                            'fiscal-position-tooltip' => 'Nafasi za kodi hurekebisha kodi na akaunti kulingana na mahali mteja alipo.',
                            'cash-rounding'           => 'Njia ya ukadiriaji wa fedha taslimu',
                            'cash-rounding-tooltip'   => 'Kipimo kidogo zaidi cha sarafu kinachoweza kulipwa kwa fedha taslimu.',
                            'auto-post'               => 'Rekodi kiotomatiki',
                            'checked'                 => 'Imekaguliwa',
                        ],
                    ],
                ],
            ],

            'term-and-conditions' => [
                'title' => 'Sheria na masharti',
            ],
        ],
    ],

    'table' => [
        'total'   => 'Jumla',
        'columns' => [
            'number'           => 'Nambari',
            'state'            => 'Hali',
            'customer'         => 'Mteja',
            'vendor'           => 'Msambazaji',
            'bill-date'        => 'Tarehe ya bili',
            'checked'          => 'Imekaguliwa',
            'accounting-date'  => 'Uhasibu',
            'due-date'         => 'Tarehe ya mwisho',
            'source-document'  => 'Hati chanzo',
            'reference'        => 'Rejea',
            'sales-person'     => 'Muuzaji',
            'tax-excluded'     => 'Bila kodi',
            'tax'              => 'Kodi',
            'total'            => 'Jumla',
            'amount-due'       => 'Kiasi kinachodaiwa',
            'amount-paid'      => 'Kilicholipwa',
            'bill-currency'    => 'Sarafu ya bili',
        ],

        'summarizers' => [
            'total' => 'Jumla',
        ],

        'groups' => [
            'name'                         => 'Jina',
            'bill-partner-display-name'    => 'Jina la mhusika kwenye bili',
            'bill-date'                    => 'Tarehe ya bili',
            'checked'                      => 'Imekaguliwa',
            'date'                         => 'Tarehe',
            'bill-due-date'                => 'Tarehe ya mwisho ya bili',
            'bill-origin'                  => 'Chanzo cha bili',
            'sales-person'                 => 'Muuzaji',
            'currency'                     => 'Sarafu',
            'created-at'                   => 'Iliundwa',
            'updated-at'                   => 'Ilisasishwa',
        ],

        'filters' => [
            'number'                    => 'Nambari',
            'bill-partner-display-name' => 'Jina la mhusika kwenye bili',
            'bill-date'                 => 'Tarehe ya bili',
            'bill-due-date'             => 'Tarehe ya mwisho ya bili',
            'bill-origin'               => 'Chanzo cha bili',
            'reference'                 => 'Rejea',
            'payment-reference'         => 'Rejea ya malipo',
            'narration'                 => 'Maelezo',
            'partner'                   => 'Mhusika',
            'journal'                   => 'Jarida',
            'fiscal-position'           => 'Nafasi ya kodi',
            'currency'                  => 'Sarafu',
            'company'                   => 'Kampuni',
            'date'                      => 'Tarehe ya uhasibu',
            'delivery-date'             => 'Tarehe ya uwasilishaji',
            'amount-untaxed'            => 'Kiasi bila kodi',
            'amount-tax'                => 'Kiasi cha kodi',
            'amount-total'              => 'Kiasi cha jumla',
            'amount-residual'           => 'Kiasi kinachodaiwa',
            'checked'                   => 'Imekaguliwa',
            'posted-before'             => 'Ilirekodiwa awali',
            'is-move-sent'              => 'Imetumwa',
            'created-at'                => 'Iliundwa',
            'updated-at'                => 'Ilisasishwa',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Malipo yamefutwa',
                    'body'  => 'Malipo yamefutwa kikamilifu.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Malipo yamefutwa',
                    'body'  => 'Bili zimefutwa kwa mafanikio.',
                ],
            ],
        ],

        'toolbar-actions' => [
            'export' => [
                'label' => 'Hamisha',
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
                    'journal'           => 'Jarida',
                    'currency'          => 'Sarafu',
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

                        'entries' => [
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
                    'accounting' => [
                        'title' => 'Uhasibu',

                        'entries' => [
                            'company'           => 'Kampuni',
                            'incoterm'          => 'Incoterm',
                            'incoterm-location' => 'Mahali pa Incoterm',
                            'payment-method'    => 'Njia ya malipo',
                            'checked'           => 'Imekaguliwa',
                            'fiscal-position'   => 'Nafasi ya kodi',
                            'cash-rounding'     => 'Njia ya ukadiriaji wa fedha taslimu',
                        ],
                    ],
                ],
            ],

            'term-and-conditions' => [
                'title' => 'Sheria na masharti',
            ],

            'journal-items' => [
                'title' => 'Vipengee vya jarida',

                'repeater' => [
                    'entries' => [
                        'account'  => 'Akaunti',
                        'partner'  => 'Mhusika',
                        'label'    => 'Maelezo',
                        'due-date' => 'Tarehe ya mwisho',
                        'currency' => 'Sarafu',
                        'taxes'    => 'Kodi',
                        'debit'    => 'Debiti',
                        'credit'   => 'Krediti',
                    ],
                ],
            ],
        ],
    ],
];
