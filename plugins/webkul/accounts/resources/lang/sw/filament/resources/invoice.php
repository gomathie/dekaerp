<?php

return [
    'title' => 'Ankara',

    'navigation' => [
        'title' => 'Ankara',
        'group' => 'Ankara',
    ],

    'global-search' => [
        'customer' => 'Mteja',
        'date'     => 'Tarehe',
        'due-date' => 'Tarehe ya mwisho',
    ],

    'form' => [
        'section' => [
            'general' => [
                'title'  => 'Taarifa za jumla',

                'fields' => [
                    'customer-invoice' => 'Ankara ya mteja',
                    'customer'         => 'Mteja',
                    'invoice-date'     => 'Tarehe ya ankara',
                    'due-date'         => 'Tarehe ya mwisho',
                    'payment-term'     => 'Sharti la malipo',
                    'journal'          => 'Jarida',
                    'currency'         => 'Sarafu',
                ],
            ],
        ],

        'notifications' => [
            'missing-rate' => [
                'title' => 'Hakuna kiwango cha ubadilishaji',
                'body'  => 'Hakuna kiwango cha ubadilishaji kati ya :from na :to, kwa hiyo bei hazikubadilishwa. Ongeza kiwango chini ya Mipangilio → Sarafu, au weka bei kwa mkono.',
            ],
        ],

        'tabs' => [
            'invoice-lines' => [
                'title' => 'Mistari ya ankara',

                'repeater' => [
                    'products' => [
                        'title'       => 'Bidhaa',
                        'add-product' => 'Ongeza bidhaa',
                        'price-hint'  => 'Bei ya kipimo ikibadilishwa hapa inahusu ankara hii pekee. Bidhaa inabaki na bei yake.',

                        'columns' => [
                            'product'             => 'Bidhaa',
                            'quantity'            => 'Idadi',
                            'unit'                => 'Kipimo',
                            'taxes'               => 'Kodi',
                            'discount-percentage' => 'Punguzo',
                            'unit-price'          => 'Bei ya kipimo',
                            'sub-total'           => 'Jumla ndogo',
                        ],

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
                            'company'                 => 'Kampuni',
                            'incoterm'                => 'Incoterm',
                            'incoterm-location'       => 'Mahali pa Incoterm',
                            'fiscal-position'         => 'Nafasi ya kodi',
                            'fiscal-position-tooltip' => 'Nafasi za kodi hurekebisha kodi na akaunti kulingana na mahali mteja alipo.',
                            'cash-rounding'           => 'Njia ya ukadiriaji wa fedha taslimu',
                            'cash-rounding-tooltip'   => 'Kipimo kidogo zaidi cha sarafu kinachoweza kulipwa kwa fedha taslimu.',
                            'payment-method'          => 'Njia ya malipo',
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
            'created-by'       => 'Imeundwa na',
            'customer'         => 'Mteja',
            'invoice-date'     => 'Tarehe ya ankara',
            'checked'          => 'Imekaguliwa',
            'accounting-date'  => 'Uhasibu',
            'due-date'         => 'Tarehe ya mwisho',
            'source-document'  => 'Hati chanzo',
            'reference'        => 'Rejea',
            'sales-person'     => 'Muuzaji',
            'tax-excluded'     => 'Bila kodi',
            'tax'              => 'Kodi',
            'total'            => 'Jumla',
            'amount-paid'      => 'Imelipwa',
            'amount-due'       => 'Kiasi kinachodaiwa',
            'invoice-currency' => 'Sarafu ya ankara',
        ],

        'summarizers' => [
            'total' => 'Jumla',
        ],

        'groups' => [
            'name'                         => 'Jina',
            'invoice-partner-display-name' => 'Jina la mhusika kwenye ankara',
            'invoice-date'                 => 'Tarehe ya ankara',
            'checked'                      => 'Imekaguliwa',
            'date'                         => 'Tarehe',
            'invoice-due-date'             => 'Tarehe ya mwisho ya ankara',
            'invoice-origin'               => 'Chanzo cha ankara',
            'sales-person'                 => 'Muuzaji',
            'currency'                     => 'Sarafu',
            'created-at'                   => 'Iliundwa',
            'updated-at'                   => 'Ilisasishwa',
        ],

        'filters' => [
            'number'                       => 'Nambari',
            'invoice-partner-display-name' => 'Jina la mhusika kwenye ankara',
            'invoice-date'                 => 'Tarehe ya ankara',
            'invoice-due-date'             => 'Tarehe ya mwisho ya ankara',
            'invoice-origin'               => 'Chanzo cha ankara',
            'reference'                    => 'Rejea',
            'payment-reference'            => 'Rejea ya malipo',
            'narration'                    => 'Maelezo',
            'partner'                      => 'Mhusika',
            'journal'                      => 'Jarida',
            'fiscal-position'              => 'Nafasi ya kodi',
            'currency'                     => 'Sarafu',
            'company'                      => 'Kampuni',
            'date'                         => 'Tarehe ya uhasibu',
            'delivery-date'                => 'Tarehe ya uwasilishaji',
            'amount-untaxed'               => 'Kiasi bila kodi',
            'amount-tax'                   => 'Kiasi cha kodi',
            'amount-total'                 => 'Kiasi cha jumla',
            'amount-residual'              => 'Kiasi kinachodaiwa',
            'checked'                      => 'Imekaguliwa',
            'posted-before'                => 'Ilirekodiwa awali',
            'is-move-sent'                 => 'Imetumwa',
            'created-at'                   => 'Iliundwa',
            'updated-at'                   => 'Ilisasishwa',
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
                    'body'  => 'Malipo yote yamefutwa kikamilifu.',
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
                    'customer-invoice' => 'Ankara ya mteja',
                    'customer'         => 'Mteja',
                    'invoice-date'     => 'Tarehe ya ankara',
                    'due-date'         => 'Tarehe ya mwisho',
                    'payment-term'     => 'Sharti la malipo',
                    'journal'          => 'Jarida',
                    'currency'         => 'Sarafu',
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

                        'entries' => [
                            'company'           => 'Kampuni',
                            'incoterm'          => 'Incoterm',
                            'incoterm-location' => 'Mahali pa Incoterm',
                            'payment-method'    => 'Njia ya malipo',
                            'cash-rounding'     => 'Njia ya ukadiriaji wa fedha taslimu',
                            'fiscal-position'   => 'Nafasi ya kodi',
                            'auto-post'         => 'Rekodi kiotomatiki',
                            'checked'           => 'Imekaguliwa',
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
                        'currency' => 'Sarafu',
                        'due-date' => 'Tarehe ya mwisho',
                        'taxes'    => 'Kodi',
                        'debit'    => 'Debiti',
                        'credit'   => 'Krediti',
                    ],
                ],
            ],
        ],
    ],

    'summary' => [
        'actions' => [
            'reconcile' => [
                'label' => 'Ongeza',
            ],

            'unreconcile' => [
                'label' => 'Tenganisha',
            ],
        ],
    ],

];
