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
        'due-date' => 'Tarehe ya malipo',
    ],

    'form' => [
        'section' => [
            'general' => [
                'title'  => 'Jumla',

                'fields' => [
                    'customer-invoice' => 'Ankara ya mteja',
                    'customer'         => 'Mteja',
                    'invoice-date'     => 'Tarehe ya ankara',
                    'due-date'         => 'Tarehe ya malipo',
                    'payment-term'     => 'Masharti ya malipo',
                    'journal'          => 'Jarida',
                    'currency'         => 'Sarafu',
                ],
            ],
        ],

        'notifications' => [
            'missing-rate' => [
                'title' => 'Hakuna kiwango cha ubadilishaji',
                'body'  => 'Hakuna kiwango cha ubadilishaji kati ya :from na :to, hivyo bei hazikubadilishwa. Ongeza kiwango chini ya Usanidi → Sarafu, au ingiza bei mwenyewe.',
            ],
        ],

        'tabs' => [
            'invoice-lines' => [
                'title' => 'Mistari ya ankara',

                'repeater' => [
                    'products' => [
                        'title'       => 'Bidhaa',
                        'add-product' => 'Ongeza bidhaa',
                        'price-hint'  => 'Bei iliyobadilishwa hapa inatumika kwa ankara hii tu. Bidhaa inabaki na bei yake.',

                        'columns' => [
                            'product'             => 'Bidhaa',
                            'quantity'            => 'Kiasi',
                            'unit'                => 'Uniti',
                            'taxes'               => 'Kodi',
                            'discount-percentage' => 'Punguzo',
                            'unit-price'          => 'Bei ya uniti',
                            'sub-total'           => 'Jumla ndogo',
                        ],

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
                            'company'                 => 'Kampuni',
                            'incoterm'                => 'Incoterm',
                            'incoterm-location'       => 'Incoterm Mahali',
                            'fiscal-position'         => 'Kifedha Cheo',
                            'fiscal-position-tooltip' => 'Kifedha positions ni inatumika hadi adapt kodi na akaunti based washa mteja mahali.',
                            'cash-rounding'           => 'Taslimu Rounding Method',
                            'cash-rounding-tooltip'   => 'Specifies smallest taslimu-ya kulipa uniti ya sarafu.',
                            'payment-method'          => 'Malipo Method',
                            'auto-post'               => 'Auto Post',
                            'checked'                 => 'Checked',
                        ],
                    ],
                ],
            ],

            'term-and-conditions' => [
                'title' => 'Term & Conditions',
            ],
        ],
    ],

    'table' => [
        'total'   => 'Jumla',
        'columns' => [
            'number'           => 'Nambari',
            'state'            => 'Hali',
            'created-by'       => 'Imeundwa Na',
            'customer'         => 'Mteja',
            'invoice-date'     => 'Tarehe ya ankara',
            'checked'          => 'Checked',
            'accounting-date'  => 'Uhasibu',
            'due-date'         => 'Tarehe ya malipo',
            'source-document'  => 'Chanzo Hati',
            'reference'        => 'Kumbukumbu',
            'sales-person'     => 'Mauzo Person',
            'tax-excluded'     => 'Kodi Haijajumuishwa',
            'tax'              => 'Kodi',
            'total'            => 'Jumla',
            'amount-due'       => 'Kiasi Due',
            'invoice-currency' => 'Ankara Sarafu',
        ],

        'summarizers' => [
            'total' => 'Jumla',
        ],

        'groups' => [
            'name'                         => 'Jina',
            'invoice-partner-display-name' => 'Ankara Mshirika Display Jina',
            'invoice-date'                 => 'Tarehe ya ankara',
            'checked'                      => 'Checked',
            'date'                         => 'Tarehe',
            'invoice-due-date'             => 'Ankara Tarehe ya malipo',
            'invoice-origin'               => 'Ankara Asili',
            'sales-person'                 => 'Mauzo Person',
            'currency'                     => 'Sarafu',
            'created-at'                   => 'Imeundwa',
            'updated-at'                   => 'Imesasishwa',
        ],

        'filters' => [
            'number'                       => 'Nambari',
            'invoice-partner-display-name' => 'Ankara Mshirika Display Jina',
            'invoice-date'                 => 'Tarehe ya ankara',
            'invoice-due-date'             => 'Ankara Tarehe ya malipo',
            'invoice-origin'               => 'Ankara Asili',
            'reference'                    => 'Kumbukumbu',
            'payment-reference'            => 'Malipo Kumbukumbu',
            'narration'                    => 'Narration',
            'partner'                      => 'Mshirika',
            'journal'                      => 'Jarida',
            'fiscal-position'              => 'Kifedha Cheo',
            'currency'                     => 'Sarafu',
            'company'                      => 'Kampuni',
            'date'                         => 'Uhasibu Tarehe',
            'delivery-date'                => 'Usafirishaji Tarehe',
            'amount-untaxed'               => 'Bila kodi Kiasi',
            'amount-tax'                   => 'Kodi Kiasi',
            'amount-total'                 => 'Jumla Kiasi',
            'amount-residual'              => 'Kiasi Due',
            'checked'                      => 'Checked',
            'posted-before'                => 'Imewekwa Kabla ya',
            'is-move-sent'                 => 'Imetumwa',
            'created-at'                   => 'Imeundwa',
            'updated-at'                   => 'Imesasishwa',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Malipo imefutwa',
                    'body'  => 'malipo ina imefutwa successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Malipo imefutwa',
                    'body'  => 'malipo ina imefutwa successfully.',
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
                'title'   => 'Jumla',
                'entries' => [
                    'customer-invoice' => 'Ankara ya mteja',
                    'customer'         => 'Mteja',
                    'invoice-date'     => 'Tarehe ya ankara',
                    'due-date'         => 'Tarehe ya malipo',
                    'payment-term'     => 'Masharti ya malipo',
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

                        'entries' => [
                            'company'           => 'Kampuni',
                            'incoterm'          => 'Incoterm',
                            'incoterm-location' => 'Incoterm Mahali',
                            'payment-method'    => 'Malipo Method',
                            'cash-rounding'     => 'Taslimu Rounding Method',
                            'fiscal-position'   => 'Kifedha Cheo',
                            'auto-post'         => 'Auto Post',
                            'checked'           => 'Checked',
                        ],
                    ],
                ],
            ],

            'term-and-conditions' => [
                'title' => 'Term & Conditions',
            ],

            'journal-items' => [
                'title' => 'Jarida Vipengee',

                'repeater' => [
                    'entries' => [
                        'account'  => 'Akaunti',
                        'partner'  => 'Mshirika',
                        'label'    => 'Lebo',
                        'currency' => 'Sarafu',
                        'due-date' => 'Tarehe ya malipo',
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
                'label' => 'Unlink',
            ],
        ],
    ],

];
