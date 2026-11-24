<?php

return [
    'form' => [
        'value'                  => 'Thamani',
        'due'                    => 'Inadaiwa',
        'delay-due'              => 'Muda wa kusubiri',
        'delay-type'             => 'Aina ya muda wa kusubiri',
        'days-on-the-next-month' => 'Siku za mwezi unaofuata',
        'days'                   => 'Siku',
        'payment-term'           => 'Sharti la malipo',
    ],

    'table' => [
        'columns' => [
            'due'          => 'Inadaiwa',
            'value'        => 'Thamani',
            'value-amount' => 'Kiasi cha thamani',
            'after'        => 'Baada ya',
            'delay-type'   => 'Aina ya muda wa kusubiri',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Awamu ya malipo imesasishwa',
                    'body'  => 'Awamu ya malipo imesasishwa kikamilifu.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Awamu ya malipo imefutwa',
                    'body'  => 'Awamu ya malipo imefutwa kikamilifu.',
                ],
            ],
        ],

        'header-actions' => [
            'create' => [
                'notification' => [
                    'title' => 'Awamu ya malipo imeundwa',
                    'body'  => 'Awamu ya malipo imeundwa kikamilifu.',
                ],
            ],
        ],
    ],
];
