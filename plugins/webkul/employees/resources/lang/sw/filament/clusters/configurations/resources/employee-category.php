<?php

return [
    'title' => 'Lebo',

    'navigation' => [
        'title' => 'Lebo',
        'group' => 'Mfanyakazi',
    ],

    'groups' => [
        'status'     => 'Hali',
        'created-by' => 'Imeundwa Na',
        'created-at' => 'Imeundwa',
        'updated-at' => 'Imesasishwa',
    ],

    'form' => [
        'fields' => [
            'name'             => 'Jina',
            'name-placeholder' => 'Ingiza jina ya lebo',
            'color'            => 'Rangi',
        ],
    ],

    'table' => [
        'columns' => [
            'id'         => 'Kitambulisho',
            'name'       => 'Jina',
            'color'      => 'Rangi',
            'created-by' => 'Imeundwa Na',
            'created-at' => 'Imeundwa',
            'updated-at' => 'Imesasishwa',
        ],

        'filters' => [
            'name'       => 'Jina',
            'created-by' => 'Imeundwa Na',
            'updated-by' => 'Imesasishwa Na',
            'updated-at' => 'Imesasishwa',
            'created-at' => 'Imeundwa',
        ],

        'groups' => [
            'name'         => 'Jina',
            'job-position' => 'Kazi Cheo',
            'color'        => 'Rangi',
            'created-by'   => 'Imeundwa Na',
            'created-at'   => 'Imeundwa',
            'updated-at'   => 'Imesasishwa',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Lebo imesasishwa',
                    'body'  => 'lebo ina imesasishwa successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Lebo imefutwa',
                    'body'  => 'lebo ina imefutwa successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Lebo imefutwa',
                    'body'  => 'lebo ina imefutwa successfully.',
                ],
            ],
        ],

        'empty-state-action' => [
            'create' => [
                'notification' => [
                    'title' => 'Lebo imeundwa',
                    'body'  => 'lebo ina imeundwa successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'name'  => 'Jina',
        'color' => 'Rangi',
    ],
];
