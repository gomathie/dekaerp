<?php

return [
    'title' => 'Maingizo ya jarida',

    'navigation' => [
        'title' => 'Maingizo ya jarida',
    ],

    'record-sub-navigation' => [
        'payment' => 'Malipo',
    ],

    'global-search' => [
        'number'   => 'Nambari',
        'partner'  => 'Mhusika',
        'date'     => 'Tarehe ya ankara',
        'due-date' => 'Tarehe ya mwisho ya ankara',
    ],

    'form' => [
        'section' => [
            'general' => [
                'title'  => 'Jumla',

                'fields' => [
                    'reference'       => 'Kumbukumbu',
                    'accounting-date' => 'Tarehe ya uhasibu',
                    'journal'         => 'Jarida',
                ],
            ],
        ],

        'tabs' => [
            'lines' => [
                'title' => 'Vipengee vya jarida',

                'repeater' => [
                    'title'       => 'Vipengee',
                    'add-item'    => 'Ongeza kipengee',

                    'columns' => [
                        'account'                  => 'Akaunti',
                        'partner'                  => 'Mhusika',
                        'label'                    => 'Lebo',
                        'amount-currency'          => 'Kiasi (sarafu)',
                        'currency'                 => 'Sarafu',
                        'taxes'                    => 'Kodi',
                        'debit'                    => 'Debiti',
                        'credit'                   => 'Krediti',
                        'discount-amount-currency' => 'Kiasi cha punguzo (sarafu)',
                    ],

                    'fields' => [
                        'account'                  => 'Akaunti',
                        'partner'                  => 'Mhusika',
                        'label'                    => 'Lebo',
                        'amount-currency'          => 'Kiasi (sarafu)',
                        'currency'                 => 'Sarafu',
                        'taxes'                    => 'Kodi',
                        'debit'                    => 'Debiti',
                        'credit'                   => 'Krediti',
                        'discount-amount-currency' => 'Kiasi cha punguzo (sarafu)',
                    ],
                ],
            ],

            'other-information' => [
                'title'    => 'Taarifa nyingine',

                'fields' => [
                    'checked'         => 'Imekaguliwa',
                    'company'         => 'Kampuni',
                    'fiscal-position' => 'Hali ya kodi',
                    'fiscal-position-tooltip' => 'Hali za kodi hutumika kurekebisha kodi na akaunti kulingana na mahali mteja alipo.',
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
            'invoice-date' => 'Tarehe ya ankara',
            'date'         => 'Tarehe',
            'number'       => 'Nambari',
            'partner'      => 'Mhusika',
            'reference'    => 'Kumbukumbu',
            'journal'      => 'Jarida',
            'company'      => 'Kampuni',
            'total'        => 'Jumla',
            'state'        => 'Hali',
            'checked'      => 'Imekaguliwa',
        ],

        'summarizers' => [
            'total' => 'Jumla',
        ],

        'groups' => [
            'partner'        => 'Mhusika',
            'journal'        => 'Jarida',
            'state'          => 'Hali',
            'payment-method' => 'Njia ya malipo',
            'date'           => 'Tarehe',
            'invoice-date'   => 'Tarehe ya ankara',
            'company'        => 'Kampuni',
        ],

        'filters' => [
            'number'                       => 'Nambari',
            'invoice-partner-display-name' => 'Jina la mhusika kwenye ankara',
            'invoice-date'                 => 'Tarehe ya ankara',
            'invoice-due-date'             => 'Tarehe ya mwisho ya ankara',
            'invoice-origin'               => 'Chanzo cha ankara',
            'reference'                    => 'Kumbukumbu',
            'created-at'                   => 'Iliundwa',
            'updated-at'                   => 'Ilisasishwa',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Malipo yamefutwa',
                    'body'  => 'Malipo yamefutwa.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Malipo yamefutwa',
                    'body'  => 'Malipo yamefutwa.',
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
                    'accounting-date' => 'Tarehe ya uhasibu',
                    'journal'         => 'Jarida',
                ],
            ],
        ],

        'tabs' => [
            'lines' => [
                'title' => 'Vipengee vya jarida',

                'repeater' => [
                    'entries' => [
                        'account'  => 'Akaunti',
                        'partner'  => 'Mhusika',
                        'label'    => 'Lebo',
                        'currency' => 'Sarafu',
                        'taxes'    => 'Kodi',
                        'debit'    => 'Debiti',
                        'credit'   => 'Krediti',
                    ],
                ],
            ],

            'other-information' => [
                'title' => 'Taarifa nyingine',

                'fieldset' => [
                    'accounting' => [
                        'title' => 'Uhasibu',

                        'entries' => [
                            'company'         => 'Kampuni',
                            'fiscal-position' => 'Hali ya kodi',
                            'checked'         => 'Imekaguliwa',
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
