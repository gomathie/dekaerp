<?php

return [
    'navigation' => [
        'title' => 'Akaunti za benki',
        'group' => 'Benki',
    ],

    'form' => [
        'account-number'     => 'Nambari ya akaunti',
        'bank'               => [
            'title'    => 'Benki',
            'sections' => [
                'general' => [
                    'title' => 'Taarifa za jumla',

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

        'account-holder'     => 'Mmiliki wa akaunti',
    ],

    'table' => [
        'columns' => [
            'account-number' => 'Nambari ya akaunti',
            'bank'           => 'Benki',
            'account-holder' => 'Mmiliki wa akaunti',
            'send-money'     => 'Inaweza kutuma pesa',
            'created-at'     => 'Iliundwa',
            'updated-at'     => 'Ilisasishwa',
            'deleted-at'     => 'Ilifutwa',
        ],

        'filters' => [
            'bank'           => 'Benki',
            'account-holder' => 'Mmiliki wa akaunti',
            'creator'        => 'Muundaji',
            'can-send-money' => 'Inaweza kutuma pesa',
        ],

        'groups' => [
            'bank'               => 'Benki',
            'can-send-money'     => 'Inaweza kutuma pesa',
            'created-at'         => 'Iliundwa',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Akaunti ya benki imesasishwa',
                    'body'  => 'Akaunti ya benki imesasishwa kikamilifu.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Akaunti ya benki imerejeshwa',
                    'body'  => 'Akaunti ya benki imerejeshwa kikamilifu.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Akaunti ya benki imefutwa',
                    'body'  => 'Akaunti ya benki imefutwa kikamilifu.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Akaunti ya benki imefutwa kabisa',
                    'body'  => 'Akaunti ya benki imefutwa kabisa kikamilifu.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Akaunti za benki zimerejeshwa',
                    'body'  => 'Akaunti za benki zimerejeshwa kikamilifu.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Akaunti za benki zimefutwa',
                    'body'  => 'Akaunti za benki zimefutwa kikamilifu.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Akaunti za benki zimefutwa kabisa',
                    'body'  => 'Akaunti za benki zimefutwa kabisa kikamilifu.',
                ],
            ],
        ],
    ],
];
