<?php

return [
    'title' => 'Uingizaji wa jarida',

    'navigation' => [
        'title' => 'Uingizaji wa jarida',
    ],

    'record-sub-navigation' => [
        'payment' => 'Malipo',
    ],

    'global-search' => [
        'number'   => 'Nambari',
        'partner'  => 'Mshirika',
        'date'     => 'Tarehe ya ankara',
        'due-date' => 'Ankara Tarehe ya malipo',
    ],

    'form' => [
        'section' => [
            'general' => [
                'title'  => 'Jumla',

                'fields' => [
                    'reference'       => 'Kumbukumbu',
                    'accounting-date' => 'Uhasibu Tarehe',
                    'journal'         => 'Jarida',
                ],
            ],
        ],

        'tabs' => [
            'lines' => [
                'title' => 'Jarida Vipengee',

                'repeater' => [
                    'title'       => 'Vipengee',
                    'add-item'    => 'Ongeza Kipengee',

                    'columns' => [
                        'account'                  => 'Akaunti',
                        'partner'                  => 'Mshirika',
                        'label'                    => 'Lebo',
                        'amount-currency'          => 'Kiasi (Sarafu)',
                        'currency'                 => 'Sarafu',
                        'taxes'                    => 'Kodi',
                        'debit'                    => 'Debiti',
                        'credit'                   => 'Krediti',
                        'discount-amount-currency' => 'Punguzo Kiasi (Sarafu)',
                    ],

                    'fields' => [
                        'account'                  => 'Akaunti',
                        'partner'                  => 'Mshirika',
                        'label'                    => 'Lebo',
                        'amount-currency'          => 'Kiasi (Sarafu)',
                        'currency'                 => 'Sarafu',
                        'taxes'                    => 'Kodi',
                        'debit'                    => 'Debiti',
                        'credit'                   => 'Krediti',
                        'discount-amount-currency' => 'Punguzo Kiasi (Sarafu)',
                    ],
                ],
            ],

            'other-information' => [
                'title'    => 'Nyingine Taarifa',

                'fields' => [
                    'checked'         => 'Checked',
                    'company'         => 'Kampuni',
                    'fiscal-position' => 'Kifedha Cheo',
                    'fiscal-position-tooltip' => 'Kifedha positions ni inatumika hadi adapt kodi na akaunti based washa mteja mahali.',
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
            'invoice-date' => 'Tarehe ya ankara',
            'date'         => 'Tarehe',
            'number'       => 'Nambari',
            'partner'      => 'Mshirika',
            'reference'    => 'Kumbukumbu',
            'journal'      => 'Jarida',
            'company'      => 'Kampuni',
            'total'        => 'Jumla',
            'state'        => 'Hali',
            'checked'      => 'Checked',
        ],

        'summarizers' => [
            'total' => 'Jumla',
        ],

        'groups' => [
            'partner'        => 'Mshirika',
            'journal'        => 'Jarida',
            'state'          => 'Hali',
            'payment-method' => 'Malipo Method',
            'date'           => 'Tarehe',
            'invoice-date'   => 'Tarehe ya ankara',
            'company'        => 'Kampuni',
        ],

        'filters' => [
            'number'                       => 'Nambari',
            'invoice-partner-display-name' => 'Ankara Mshirika Display Jina',
            'invoice-date'                 => 'Tarehe ya ankara',
            'invoice-due-date'             => 'Ankara Tarehe ya malipo',
            'invoice-origin'               => 'Ankara Asili',
            'reference'                    => 'Kumbukumbu',
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
                    'number'          => 'Nambari',
                    'reference'       => 'Kumbukumbu',
                    'accounting-date' => 'Uhasibu Tarehe',
                    'journal'         => 'Jarida',
                ],
            ],
        ],

        'tabs' => [
            'lines' => [
                'title' => 'Jarida Vipengee',

                'repeater' => [
                    'entries' => [
                        'account'  => 'Akaunti',
                        'partner'  => 'Mshirika',
                        'label'    => 'Lebo',
                        'currency' => 'Sarafu',
                        'taxes'    => 'Kodi',
                        'debit'    => 'Debiti',
                        'credit'   => 'Krediti',
                    ],
                ],
            ],

            'other-information' => [
                'title' => 'Nyingine Taarifa',

                'fieldset' => [
                    'accounting' => [
                        'title' => 'Uhasibu',

                        'entries' => [
                            'company'         => 'Kampuni',
                            'fiscal-position' => 'Kifedha Cheo',
                            'checked'         => 'Checked',
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
