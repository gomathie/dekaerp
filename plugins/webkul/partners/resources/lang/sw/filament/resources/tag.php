<?php

return [
    'navigation' => [
        'title' => 'Lebo',
    ],

    'form' => [
        'name'  => 'Jina',
        'color' => 'Rangi',
    ],

    'table' => [
        'columns' => [
            'name'       => 'Jina',
            'color'      => 'Rangi',
            'created-at' => 'Iliundwa',
            'updated-at' => 'Ilisasishwa',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Lebo imesasishwa',
                    'body'  => 'Lebo imesasishwa kwa mafanikio.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Lebo imerejeshwa',
                    'body'  => 'Lebo imerejeshwa kwa mafanikio.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Lebo imefutwa',
                    'body'  => 'Lebo imefutwa kwa mafanikio.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Lebo imefutwa kabisa',
                    'body'  => 'Lebo imefutwa kabisa kwa mafanikio.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Lebo zimerejeshwa',
                    'body'  => 'Lebo zimerejeshwa kwa mafanikio.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Lebo zimefutwa',
                    'body'  => 'Lebo zimefutwa kwa mafanikio.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Lebo zimefutwa kabisa',
                    'body'  => 'Lebo zimefutwa kabisa kwa mafanikio.',
                ],
            ],
        ],
    ],
];
