<?php

return [
    'navigation' => [
        'title' => 'Banks',
        'group' => 'Akaunti ya benkis',
    ],

    'form' => [
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

    'table' => [
        'columns' => [
            'name'           => 'Jina',
            'code'           => 'Benki Identifier Msimbo',
            'country'        => 'Nchi',
            'created-at'     => 'Imeundwa',
            'updated-at'     => 'Imesasishwa',
            'deleted-at'     => 'Imefutwa',
        ],

        'groups' => [
            'country'               => 'Nchi',
            'created-at'            => 'Imeundwa',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Benki imesasishwa',
                    'body'  => 'benki ina imesasishwa successfully.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Benki restored',
                    'body'  => 'benki ina restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Benki imefutwa',
                    'body'  => 'benki ina imefutwa successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Benki force imefutwa',
                    'body'  => 'benki ina force imefutwa successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Banks restored',
                    'body'  => 'banks ina restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Banks imefutwa',
                    'body'  => 'banks ina imefutwa successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Banks force imefutwa',
                    'body'  => 'banks ina force imefutwa successfully.',
                ],
            ],
        ],
    ],
];
