<?php

return [
    'form' => [
        'partner' => 'Mshirika',
        'name'    => 'Jina',
        'email'   => 'Barua pepe',
        'phone'   => 'Simu',
        'mobile'  => 'Mobile',
        'type'    => 'Aina',
        'address' => 'Anwani',
        'city'    => 'Jiji',
        'street1' => 'Street 1',
        'street2' => 'Street 2',
        'state'   => 'Hali',
        'zip'     => 'Zip',
        'code'    => 'Msimbo',
        'country' => 'Nchi',
    ],

    'table' => [
        'header-actions' => [
            'create' => [
                'label' => 'Ongeza Anwani',

                'notification' => [
                    'title' => 'Anwani imeundwa',
                    'body'  => 'anwani ina imeundwa successfully.',
                ],
            ],
        ],

        'columns' => [
            'type'    => 'Aina',
            'name'    => 'Mwasiliani Jina',
            'address' => 'Anwani',
            'city'    => 'Jiji',
            'street1' => 'Street 1',
            'street2' => 'Street 2',
            'state'   => 'Hali',
            'zip'     => 'Zip',
            'country' => 'Nchi',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Anwani imesasishwa',
                    'body'  => 'anwani ina imesasishwa successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Anwani imefutwa',
                    'body'  => 'anwani ina imefutwa successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Addresses imefutwa',
                    'body'  => 'addresses ina imefutwa successfully.',
                ],
            ],
        ],
    ],
];
