<?php

return [
    'global-search' => [
        'zip-from' => 'Msimbo wa posta kuanzia',
        'zip-to'   => 'Msimbo wa posta hadi',
        'name'     => 'Jina',
    ],

    'form' => [
        'fields' => [
            'name'                   => 'Jina',
            'foreign-vat'            => 'VAT ya kigeni',
            'country'                => 'Nchi',
            'country-group'          => 'Kikundi cha nchi',
            'zip-from'               => 'Msimbo wa posta kuanzia',
            'zip-to'                 => 'Msimbo wa posta hadi',
            'detect-automatically'   => 'Tambua kiotomatiki',
            'notes'                  => 'Vidokezo',
            'company'                => 'Kampuni',
        ],
        'tabs' => [
            'account-mapping' => [
                'table' => [
                    'columns' => [
                        'source-account'      => 'Akaunti chanzo',
                        'destination-account' => 'Akaunti lengwa',
                    ],
                ],

            ],
            'tax-mapping' => [
                'table' => [
                    'columns' => [
                        'tax-source'      => 'Kodi chanzo',
                        'tax-destination' => 'Kodi lengwa',
                    ],
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'                 => 'Jina',
            'company'              => 'Kampuni',
            'country'              => 'Nchi',
            'country-group'        => 'Kikundi cha nchi',
            'created-by'           => 'Imeundwa na',
            'zip-from'             => 'Msimbo wa posta kuanzia',
            'zip-to'               => 'Msimbo wa posta hadi',
            'status'               => 'Hali',
            'detect-automatically' => 'Tambua kiotomatiki',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Sharti la malipo limefutwa',
                    'body'  => 'Sharti la malipo limefutwa kikamilifu.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Nafasi ya kodi imefutwa',
                    'body'  => 'Nafasi ya kodi imefutwa kikamilifu.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'name'                 => 'Jina',
            'foreign-vat'          => 'VAT ya kigeni',
            'country'              => 'Nchi',
            'country-group'        => 'Kikundi cha nchi',
            'zip-from'             => 'Msimbo wa posta kuanzia',
            'zip-to'               => 'Msimbo wa posta hadi',
            'detect-automatically' => 'Tambua kiotomatiki',
            'notes'                => 'Vidokezo',
        ],
    ],
];
