<?php

return [
    'form' => [
        'partner' => 'Mhusika',
        'name'    => 'Jina',
        'email'   => 'Barua pepe',
        'phone'   => 'Simu',
        'mobile'  => 'Simu ya mkononi',
        'type'    => 'Aina',
        'address' => 'Anwani',
        'city'    => 'Jiji',
        'street1' => 'Mtaa 1',
        'street2' => 'Mtaa 2',
        'state'   => 'Mkoa',
        'zip'     => 'Msimbo wa posta',
        'code'    => 'Msimbo',
        'country' => 'Nchi',
    ],

    'table' => [
        'header-actions' => [
            'create' => [
                'label' => 'Ongeza anwani',

                'notification' => [
                    'title' => 'Anwani imeundwa',
                    'body'  => 'Anwani imeundwa kwa mafanikio.',
                ],
            ],
        ],

        'columns' => [
            'type'    => 'Aina',
            'name'    => 'Jina la mhusika',
            'address' => 'Anwani',
            'city'    => 'Jiji',
            'street1' => 'Mtaa 1',
            'street2' => 'Mtaa 2',
            'state'   => 'Mkoa',
            'zip'     => 'Msimbo wa posta',
            'country' => 'Nchi',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Anwani imesasishwa',
                    'body'  => 'Anwani imesasishwa kwa mafanikio.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Anwani imefutwa',
                    'body'  => 'Anwani imefutwa kwa mafanikio.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Anwani zimefutwa',
                    'body'  => 'Anwani zimefutwa kwa mafanikio.',
                ],
            ],
        ],
    ],
];
