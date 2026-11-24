<?php

return [
    'form' => [
        'fields' => [
            'color'         => 'Rangi',
            'country'       => 'Nchi',
            'applicability' => 'Matumizi',
            'name'          => 'Jina',
            'status'        => 'Hali',
            'tax-negate'    => 'Geuza kodi',
        ],
    ],

    'table' => [
        'columns' => [
            'color'         => 'Rangi',
            'country'       => 'Nchi',
            'created-by'    => 'Imeundwa na',
            'applicability' => 'Matumizi',
            'name'          => 'Jina',
            'status'        => 'Hali',
            'tax-negate'    => 'Geuza kodi',
            'created-at'    => 'Iliundwa',
            'updated-at'    => 'Ilisasishwa',
            'deleted-at'    => 'Ilifutwa',
        ],

        'filters' => [
            'bank'           => 'Benki',
            'account-holder' => 'Mmiliki wa akaunti',
            'creator'        => 'Muundaji',
            'can-send-money' => 'Inaweza kutuma pesa',
        ],

        'groups' => [
            'country'       => 'Nchi',
            'created-by'    => 'Imeundwa na',
            'applicability' => 'Matumizi',
            'name'          => 'Jina',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Lebo ya akaunti imesasishwa',
                    'body'  => 'Lebo ya akaunti imesasishwa kikamilifu.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Lebo ya akaunti imefutwa',
                    'body'  => 'Lebo ya akaunti imefutwa kikamilifu.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Lebo za akaunti zimefutwa',
                    'body'  => 'Lebo za akaunti zimefutwa kikamilifu.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'color'         => 'Rangi',
            'country'       => 'Nchi',
            'applicability' => 'Matumizi',
            'name'          => 'Jina',
            'status'        => 'Hali',
            'tax-negate'    => 'Geuza kodi',
        ],
    ],
];
