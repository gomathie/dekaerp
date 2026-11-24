<?php

return [
    'navigation' => [
        'title' => 'Benki',
        'group' => 'Akaunti za benki',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Jumla',

                'fields' => [
                    'name'  => 'Jina',
                    'code'  => 'Msimbo wa utambulisho wa benki',
                    'email' => 'Barua pepe',
                    'phone' => 'Simu',
                ],
            ],

            'address' => [
                'title' => 'Anwani',

                'fields' => [
                    'address' => 'Anwani',
                    'city'    => 'Jiji',
                    'street1' => 'Mtaa 1',
                    'street2' => 'Mtaa 2',
                    'state'   => 'Mkoa',
                    'zip'     => 'Msimbo wa posta',
                    'country' => 'Nchi',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'           => 'Jina',
            'code'           => 'Msimbo wa utambulisho wa benki',
            'country'        => 'Nchi',
            'created-at'     => 'Iliundwa',
            'updated-at'     => 'Ilisasishwa',
            'deleted-at'     => 'Ilifutwa',
        ],

        'groups' => [
            'country'               => 'Nchi',
            'created-at'            => 'Iliundwa',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Benki imesasishwa',
                    'body'  => 'Benki imesasishwa kikamilifu.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Benki imerejeshwa',
                    'body'  => 'Benki imerejeshwa kikamilifu.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Benki imefutwa',
                    'body'  => 'Benki imefutwa kikamilifu.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Benki imefutwa kabisa',
                    'body'  => 'Benki imefutwa kabisa.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Benki zimerejeshwa',
                    'body'  => 'Benki zimerejeshwa kikamilifu.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Benki zimefutwa',
                    'body'  => 'Benki zimefutwa kikamilifu.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Benki zimefutwa kabisa',
                    'body'  => 'Benki zimefutwa kabisa.',
                ],
            ],
        ],
    ],
];
