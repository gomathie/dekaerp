<?php

return [
    'title' => 'Capacity Na Bidhaa',

    'form' => [
        'product' => 'Bidhaa',
        'qty'     => 'Kiasi',
    ],

    'table' => [
        'header-actions' => [
            'create' => [
                'label' => 'Ongeza bidhaa Capacity',

                'notification' => [
                    'title' => 'Bidhaa Capacity imeundwa',
                    'body'  => 'bidhaa capacity added successfully.',
                ],
            ],
        ],

        'columns' => [
            'product' => 'Bidhaa',
            'qty'     => 'Kiasi',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Bidhaa Capacity imesasishwa',
                    'body'  => 'bidhaa capacity ina imesasishwa successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Bidhaa Capacity imefutwa',
                    'body'  => 'bidhaa capacity ina imefutwa successfully.',
                ],
            ],
        ],
    ],
];
