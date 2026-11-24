<?php

return [
    'title' => 'Uwezo kwa bidhaa',

    'form' => [
        'product' => 'Bidhaa',
        'qty'     => 'Idadi',
    ],

    'table' => [
        'header-actions' => [
            'create' => [
                'label' => 'Ongeza uwezo wa bidhaa',

                'notification' => [
                    'title' => 'Uwezo wa bidhaa umeundwa',
                    'body'  => 'Uwezo wa bidhaa umeongezwa kwa mafanikio.',
                ],
            ],
        ],

        'columns' => [
            'product' => 'Bidhaa',
            'qty'     => 'Idadi',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Uwezo wa bidhaa umesasishwa',
                    'body'  => 'Uwezo wa bidhaa umesasishwa kwa mafanikio.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Uwezo wa bidhaa umefutwa',
                    'body'  => 'Uwezo wa bidhaa umefutwa kwa mafanikio.',
                ],
            ],
        ],
    ],
];
