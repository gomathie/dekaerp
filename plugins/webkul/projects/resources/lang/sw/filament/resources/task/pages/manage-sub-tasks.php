<?php

return [
    'title' => 'Kazi ndogo',

    'table' => [
        'header-actions' => [
            'create' => [
                'label' => 'Ongeza kazi ndogo',

                'notification' => [
                    'title' => 'Kazi imeundwa',
                    'body'  => 'Kazi imeundwa kwa mafanikio.',
                ],
            ],
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Kazi imerejeshwa',
                    'body'  => 'Kazi imerejeshwa kwa mafanikio.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Kazi imefutwa',
                    'body'  => 'Kazi imefutwa kwa mafanikio.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Kazi imefutwa kabisa',
                    'body'  => 'Kazi imefutwa kabisa.',
                ],
            ],
        ],
    ],
];
