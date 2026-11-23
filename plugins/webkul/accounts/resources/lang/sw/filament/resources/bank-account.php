<?php

return [
    'navigation' => [
        'title' => 'Akaunti ya benkis',
        'group' => 'Banks',
    ],

    'form' => [
        'account-number'     => 'Akaunti Nambari',
        'bank'               => [
            'title'    => 'Benki',
            'sections' => [
                'general' => [
                    'title' => 'Jumla',

                    'fields' => [
                        'name'  => 'Jina',
                        'code'  => 'Benki Identifier Msimbo',
                        'email' => 'Barua pepe',
                        'phone' => 'Simu',
                    ],
                ],

                'address' => [
                    'title' => 'Anwani',

                    'fields' => [
                        'address' => 'Anwani',
                        'city'    => 'Jiji',
                        'street1' => 'Street 1',
                        'street2' => 'Street 2',
                        'state'   => 'Hali',
                        'zip'     => 'Zip',
                        'country' => 'Nchi',
                    ],
                ],
            ],
        ],

        'account-holder'     => 'Akaunti Holder',
    ],

    'table' => [
        'columns' => [
            'account-number' => 'Akaunti Nambari',
            'bank'           => 'Benki',
            'account-holder' => 'Akaunti Holder',
            'send-money'     => 'Unaweza Send Money',
            'created-at'     => 'Imeundwa',
            'updated-at'     => 'Imesasishwa',
            'deleted-at'     => 'Imefutwa',
        ],

        'filters' => [
            'bank'           => 'Benki',
            'account-holder' => 'Akaunti Holder',
            'creator'        => 'Creator',
            'can-send-money' => 'Unaweza Send Money',
        ],

        'groups' => [
            'bank'               => 'Benki',
            'can-send-money'     => 'Unaweza Send Money',
            'created-at'         => 'Imeundwa',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Akaunti ya benki imesasishwa',
                    'body'  => 'akaunti ya benki ina imesasishwa successfully.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Akaunti ya benki restored',
                    'body'  => 'akaunti ya benki ina restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Akaunti ya benki imefutwa',
                    'body'  => 'akaunti ya benki ina imefutwa successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Akaunti ya benki force imefutwa',
                    'body'  => 'akaunti ya benki ina force imefutwa successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Akaunti ya benkis restored',
                    'body'  => 'akaunti ya benkis ina restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Akaunti ya benkis imefutwa',
                    'body'  => 'akaunti ya benkis ina imefutwa successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Akaunti ya benkis force imefutwa',
                    'body'  => 'akaunti ya benkis ina force imefutwa successfully.',
                ],
            ],
        ],
    ],
];
