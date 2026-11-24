<?php

return [
    'title' => 'Malipo',

    'navigation' => [
        'title' => 'Malipo',
        'group' => 'Ankara',
    ],

    'global-search' => [
        'partner' => 'Mshirika',
        'amount'  => 'Kiasi',
        'date'    => 'Tarehe',
    ],

    'form' => [
        'sections' => [
            'fields' => [
                'payment-type'          => 'Malipo Aina',
                'memo'                  => 'Memo',
                'date'                  => 'Tarehe',
                'amount'                => 'Kiasi',
                'currency'              => 'Sarafu',
                'payment-method'        => 'Malipo Method',
                'customer'              => 'Mteja',
                'vendor'                => 'Msambazaji',
                'journal'               => 'Jarida',
                'customer-bank-account' => 'Mteja Akaunti ya benki',
                'vendor-bank-account'   => 'Msambazaji Akaunti ya benki',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'            => 'Jina',
            'date'            => 'Tarehe',
            'journal'         => 'Jarida',
            'payment-method'  => 'Malipo Method',
            'partner'         => 'Mshirika',
            'amount-currency' => 'Kiasi (Sarafu)',
            'amount'          => 'Kiasi',
            'state'           => 'Hali',
            'company'         => 'Kampuni',
            'currency'        => 'Sarafu',
            'created-by'      => 'Imeundwa Na',
            'invoices'        => 'Ankara',
            'unused'          => 'Kiasi kisichotumika',
        ],

        'summaries' => [
            'total' => 'Jumla',
        ],

        'groups' => [
            'name'                             => 'Jina',
            'company'                          => 'Kampuni',
            'journal'                          => 'Jarida',
            'partner'                          => 'Mshirika',
            'payment-method-line'              => 'Malipo Method Mstari',
            'payment-method'                   => 'Malipo Method',
            'partner-bank-account'             => 'Mshirika Akaunti ya benki',
            'created-at'                       => 'Imeundwa',
            'updated-at'                       => 'Imesasishwa',
        ],

        'filters' => [
            'company'                          => 'Kampuni',
            'journal'                          => 'Jarida',
            'customer-bank-account'            => 'Mteja Akaunti ya benki',
            'payment-method'                   => 'Malipo Method',
            'currency'                         => 'Sarafu',
            'partner'                          => 'Mshirika',
            'payment-method-line'              => 'Malipo Method Mstari',
            'created-at'                       => 'Imeundwa',
            'updated-at'                       => 'Imesasishwa',
            'year'                             => 'Mwaka',
            'month'                            => 'Mwezi',
            'any-year'                         => 'Mwaka wowote',
            'whole-year'                       => 'Mwaka mzima',
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
        'sections' => [
            'payment-information' => [
                'title'   => 'Malipo Taarifa',
                'entries' => [
                    'state'                 => 'Hali',
                    'vendor'                => 'Msambazaji',
                    'customer'              => 'Mteja',
                    'payment-type'          => 'Malipo Aina',
                    'journal'               => 'Jarida',
                    'customer-bank-account' => 'Mteja Akaunti ya benki',
                    'vendor-bank-account'   => 'Msambazaji Akaunti ya benki',
                    'amount'                => 'Kiasi',
                    'payment-method'        => 'Malipo Method',
                    'date'                  => 'Tarehe',
                    'memo'                  => 'Memo',
                ],
            ],
        ],
    ],

];
