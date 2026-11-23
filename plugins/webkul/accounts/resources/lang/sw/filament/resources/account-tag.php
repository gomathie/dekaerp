<?php

return [
    'form' => [
        'fields' => [
            'color'         => 'Rangi',
            'country'       => 'Nchi',
            'applicability' => 'Applicability',
            'name'          => 'Jina',
            'status'        => 'Hali',
            'tax-negate'    => 'Kodi Negate',
        ],
    ],

    'table' => [
        'columns' => [
            'color'         => 'Rangi',
            'country'       => 'Nchi',
            'created-by'    => 'Imeundwa Na',
            'applicability' => 'Applicability',
            'name'          => 'Jina',
            'status'        => 'Hali',
            'tax-negate'    => 'Kodi Negate',
            'created-at'    => 'Imeundwa',
            'updated-at'    => 'Imesasishwa',
            'deleted-at'    => 'Imefutwa',
        ],

        'filters' => [
            'bank'           => 'Benki',
            'account-holder' => 'Akaunti Holder',
            'creator'        => 'Creator',
            'can-send-money' => 'Unaweza Send Money',
        ],

        'groups' => [
            'country'       => 'Nchi',
            'created-by'    => 'Imeundwa Na',
            'applicability' => 'Applicability',
            'name'          => 'Jina',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Akaunti Lebo imesasishwa',
                    'body'  => 'akaunti Lebo ina imesasishwa successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Akaunti Lebo imefutwa',
                    'body'  => 'akaunti Lebo ina imefutwa successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Akaunti Lebo imefutwa',
                    'body'  => 'akaunti Lebo ina imefutwa successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'color'         => 'Rangi',
            'country'       => 'Nchi',
            'applicability' => 'Applicability',
            'name'          => 'Jina',
            'status'        => 'Hali',
            'tax-negate'    => 'Kodi Negate',
        ],
    ],
];
