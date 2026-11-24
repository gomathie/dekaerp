<?php

return [
    'title' => 'Malipo',

    'navigation' => [
        'title' => 'Malipo',
        'group' => 'Ankara',
    ],

    'global-search' => [
        'partner' => 'Mhusika',
        'amount'  => 'Kiasi',
        'date'    => 'Tarehe',
    ],

    'form' => [
        'sections' => [
            'fields' => [
                'payment-type'          => 'Aina ya malipo',
                'memo'                  => 'Kumbukumbu',
                'date'                  => 'Tarehe',
                'amount'                => 'Kiasi',
                'currency'              => 'Sarafu',
                'payment-method'        => 'Njia ya malipo',
                'customer'              => 'Mteja',
                'vendor'                => 'Msambazaji',
                'journal'               => 'Jarida',
                'customer-bank-account' => 'Akaunti ya benki ya mteja',
                'vendor-bank-account'   => 'Akaunti ya benki ya msambazaji',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'            => 'Jina',
            'date'            => 'Tarehe',
            'journal'         => 'Jarida',
            'payment-method'  => 'Njia ya malipo',
            'partner'         => 'Mhusika',
            'amount-currency' => 'Kiasi (kwa sarafu)',
            'amount'          => 'Kiasi',
            'state'           => 'Hali',
            'company'         => 'Kampuni',
            'currency'        => 'Sarafu',
            'created-by'      => 'Imeundwa na',
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
            'partner'                          => 'Mhusika',
            'payment-method-line'              => 'Mstari wa njia ya malipo',
            'payment-method'                   => 'Njia ya malipo',
            'partner-bank-account'             => 'Akaunti ya benki ya mhusika',
            'created-at'                       => 'Iliundwa',
            'updated-at'                       => 'Ilisasishwa',
        ],

        'filters' => [
            'company'                          => 'Kampuni',
            'journal'                          => 'Jarida',
            'customer-bank-account'            => 'Akaunti ya benki ya mteja',
            'payment-method'                   => 'Njia ya malipo',
            'currency'                         => 'Sarafu',
            'partner'                          => 'Mhusika',
            'payment-method-line'              => 'Mstari wa njia ya malipo',
            'created-at'                       => 'Iliundwa',
            'updated-at'                       => 'Ilisasishwa',
            'year'                             => 'Mwaka',
            'month'                            => 'Mwezi',
            'any-year'                         => 'Mwaka wowote',
            'whole-year'                       => 'Mwaka mzima',
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
        'sections' => [
            'payment-information' => [
                'title'   => 'Taarifa za malipo',
                'entries' => [
                    'state'                 => 'Hali',
                    'vendor'                => 'Msambazaji',
                    'customer'              => 'Mteja',
                    'payment-type'          => 'Aina ya malipo',
                    'journal'               => 'Jarida',
                    'customer-bank-account' => 'Akaunti ya benki ya mteja',
                    'vendor-bank-account'   => 'Akaunti ya benki ya msambazaji',
                    'amount'                => 'Kiasi',
                    'payment-method'        => 'Njia ya malipo',
                    'date'                  => 'Tarehe',
                    'memo'                  => 'Kumbukumbu',
                ],
            ],
        ],
    ],

];
