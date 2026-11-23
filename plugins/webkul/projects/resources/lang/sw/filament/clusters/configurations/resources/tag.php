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
            'created-at' => 'Imeundwa',
            'updated-at' => 'Imesasishwa',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Lebo imesasishwa',
                    'body'  => 'lebo ina imesasishwa successfully.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Lebo restored',
                    'body'  => 'lebo ina restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Lebo imefutwa',
                    'body'  => 'lebo ina imefutwa successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Lebo force imefutwa',
                    'body'  => 'lebo ina force imefutwa successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Lebo restored',
                    'body'  => 'lebo ina restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Lebo imefutwa',
                    'body'  => 'lebo ina imefutwa successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Lebo force imefutwa',
                    'body'  => 'lebo ina force imefutwa successfully.',
                ],
            ],
        ],
    ],
];
