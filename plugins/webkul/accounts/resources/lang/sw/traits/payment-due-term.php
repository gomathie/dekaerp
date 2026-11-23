<?php

return [
    'form' => [
        'value'                  => 'Thamani',
        'due'                    => 'Due',
        'delay-due'              => 'Delay Due',
        'delay-type'             => 'Delay Aina',
        'days-on-the-next-month' => 'Siku washa ifuatayo mwezi',
        'days'                   => 'Siku',
        'payment-term'           => 'Masharti ya malipo',
    ],

    'table' => [
        'columns' => [
            'due'          => 'Due',
            'value'        => 'Thamani',
            'value-amount' => 'Thamani Kiasi',
            'after'        => 'Baada ya',
            'delay-type'   => 'Delay Aina',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Malipo Due Term imesasishwa',
                    'body'  => 'malipo due term ina imesasishwa successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Malipo Due Term imefutwa',
                    'body'  => 'malipo due term ina imefutwa successfully.',
                ],
            ],
        ],

        'header-actions' => [
            'create' => [
                'notification' => [
                    'title' => 'Malipo Due Term imeundwa',
                    'body'  => 'malipo due term ina imeundwa successfully.',
                ],
            ],
        ],
    ],
];
