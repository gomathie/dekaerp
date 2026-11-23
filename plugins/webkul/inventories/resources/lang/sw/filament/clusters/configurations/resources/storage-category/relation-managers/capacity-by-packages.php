<?php

return [
    'title' => 'Capacity Na Vifurushi',

    'form' => [
        'package-type' => 'Kifurushi Aina',
        'qty'          => 'Kiasi',
    ],

    'table' => [
        'header-actions' => [
            'create' => [
                'label' => 'Ongeza Kifurushi Aina Capacity',

                'notification' => [
                    'title' => 'Kifurushi Aina Capacity imeundwa',
                    'body'  => 'kifurushi aina capacity added successfully.',
                ],
            ],
        ],

        'columns' => [
            'package-type' => 'Kifurushi Aina',
            'qty'          => 'Kiasi',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Kifurushi Aina Capacity imesasishwa',
                    'body'  => 'kifurushi aina capacity ina imesasishwa successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Kifurushi Aina Capacity imefutwa',
                    'body'  => 'kifurushi aina capacity ina imefutwa successfully.',
                ],
            ],
        ],
    ],
];
