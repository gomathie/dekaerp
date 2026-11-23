<?php

return [
    'title' => 'Ankara',

    'navigation' => [
        'title' => 'Ankara',
        'group' => 'Ankara',
    ],

    'global-search' => [
        'vendor'   => 'Msambazaji',
        'date'     => 'Tarehe',
        'due-date' => 'Tarehe ya malipo',
    ],

    'form' => [
        'section' => [
            'general' => [
                'title'  => 'Jumla',
                'fields' => [
                    'vendor-bill'       => 'Bili ya msambazaji',
                    'vendor'            => 'Msambazaji',
                    'bill-date'         => 'Bili Tarehe',
                    'bill-reference'    => 'Bili Kumbukumbu',
                    'accounting-date'   => 'Uhasibu Tarehe',
                    'payment-reference' => 'Malipo Kumbukumbu',
                    'recipient-bank'    => 'Mpokeaji Benki',
                    'due-date'          => 'Tarehe ya malipo',
                    'payment-term'      => 'Masharti ya malipo',
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
                        'add-product' => 'Ongeza bidhaa',

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
                    'accounting' => [
                        'title' => 'Uhasibu',

                        'fields' => [
                            'company'                 => 'Kampuni',
                            'incoterm'                => 'Incoterm',
                            'incoterm-location'       => 'Incoterm Mahali',
                            'payment-method'          => 'Malipo Method',
                            'fiscal-position'         => 'Kifedha Cheo',
                            'fiscal-position-tooltip' => 'Kifedha positions ni inatumika hadi adapt kodi na akaunti based washa mteja mahali.',
                            'cash-rounding'           => 'Taslimu Rounding Method',
                            'cash-rounding-tooltip'   => 'Specifies smallest taslimu-ya kulipa uniti ya sarafu.',
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
            'customer'         => 'Mteja',
            'bill-date'        => 'Bili Tarehe',
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
            'bill-currency'    => 'Bili Sarafu',
        ],

        'summarizers' => [
            'total' => 'Jumla',
        ],

        'groups' => [
            'name'                         => 'Jina',
            'bill-partner-display-name'    => 'Bili Mshirika Display Jina',
            'bill-date'                    => 'Bili Tarehe',
            'checked'                      => 'Checked',
            'date'                         => 'Tarehe',
            'bill-due-date'                => 'Bili Tarehe ya malipo',
            'bill-origin'                  => 'Bili Asili',
            'sales-person'                 => 'Mauzo Person',
            'currency'                     => 'Sarafu',
            'created-at'                   => 'Imeundwa',
            'updated-at'                   => 'Imesasishwa',
        ],

        'filters' => [
            'number'                    => 'Nambari',
            'bill-partner-display-name' => 'Bili Mshirika Display Jina',
            'bill-date'                 => 'Bili Tarehe',
            'bill-due-date'             => 'Bili Tarehe ya malipo',
            'bill-origin'               => 'Bili Asili',
            'reference'                 => 'Kumbukumbu',
            'payment-reference'         => 'Malipo Kumbukumbu',
            'narration'                 => 'Narration',
            'partner'                   => 'Mshirika',
            'journal'                   => 'Jarida',
            'fiscal-position'           => 'Kifedha Cheo',
            'currency'                  => 'Sarafu',
            'company'                   => 'Kampuni',
            'date'                      => 'Uhasibu Tarehe',
            'delivery-date'             => 'Usafirishaji Tarehe',
            'amount-untaxed'            => 'Bila kodi Kiasi',
            'amount-tax'                => 'Kodi Kiasi',
            'amount-total'              => 'Jumla Kiasi',
            'amount-residual'           => 'Kiasi Due',
            'checked'                   => 'Checked',
            'posted-before'             => 'Imewekwa Kabla ya',
            'is-move-sent'              => 'Imetumwa',
            'created-at'                => 'Imeundwa',
            'updated-at'                => 'Imesasishwa',
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
                    'vendor-invoice'    => 'Msambazaji Ankara',
                    'vendor'            => 'Msambazaji',
                    'bill-date'         => 'Bili Tarehe',
                    'bill-reference'    => 'Bili Kumbukumbu',
                    'accounting-date'   => 'Uhasibu Tarehe',
                    'payment-reference' => 'Malipo Kumbukumbu',
                    'recipient-bank'    => 'Mpokeaji Benki',
                    'due-date'          => 'Tarehe ya malipo',
                    'payment-term'      => 'Masharti ya malipo',
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
                        'add-product' => 'Ongeza bidhaa',

                        'entries' => [
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
                    'accounting' => [
                        'title' => 'Uhasibu',

                        'entries' => [
                            'company'           => 'Kampuni',
                            'incoterm'          => 'Incoterm',
                            'incoterm-location' => 'Incoterm Mahali',
                            'payment-method'    => 'Malipo Method',
                            'checked'           => 'Checked',
                            'fiscal-position'   => 'Kifedha Cheo',
                            'cash-rounding'     => 'Taslimu Rounding Method',
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
                        'due-date' => 'Tarehe ya malipo',
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
