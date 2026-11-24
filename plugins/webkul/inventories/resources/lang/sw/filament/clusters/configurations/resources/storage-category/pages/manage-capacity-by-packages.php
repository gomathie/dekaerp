<?php

return [
    'title' => 'Uwezo kwa vifurushi',

    'form' => [
        'package-type' => 'Aina ya kifurushi',
        'qty'          => 'Idadi',
    ],

    'table' => [
        'header-actions' => [
            'create' => [
                'label' => 'Ongeza uwezo wa aina ya kifurushi',

                'notification' => [
                    'title' => 'Uwezo wa aina ya kifurushi umeundwa',
                    'body'  => 'Uwezo wa aina ya kifurushi umeongezwa kwa mafanikio.',
                ],
            ],
        ],

        'columns' => [
            'package-type' => 'Aina ya kifurushi',
            'qty'          => 'Idadi',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Uwezo wa aina ya kifurushi umesasishwa',
                    'body'  => 'Uwezo wa aina ya kifurushi umesasishwa kwa mafanikio.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Uwezo wa aina ya kifurushi umefutwa',
                    'body'  => 'Uwezo wa aina ya kifurushi umefutwa kwa mafanikio.',
                ],
            ],
        ],
    ],
];
