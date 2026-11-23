<?php

return [
    'title' => 'Degrees',

    'navigation' => [
        'title' => 'Degrees',
        'group' => 'Applications',
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
            'name-placeholder' => 'Ingiza jina ya degree',
        ],
    ],

    'table' => [
        'columns' => [
            'id'         => 'Kitambulisho',
            'name'       => 'Jina',
            'created-by' => 'Imeundwa Na',
            'created-at' => 'Imeundwa',
            'updated-at' => 'Imesasishwa',
        ],

        'filters' => [
            'name'       => 'Jina',
            'employee'   => 'Mfanyakazi',
            'created-by' => 'Imeundwa Na',
            'updated-at' => 'Imesasishwa',
            'created-at' => 'Imeundwa',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Degree imesasishwa',
                    'body'  => 'degree ina imesasishwa successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Degree imefutwa',
                    'body'  => 'degree ina imefutwa successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Degrees imefutwa',
                    'body'  => 'degrees ina imefutwa successfully.',
                ],
            ],
        ],

        'empty-state-action' => [
            'create' => [
                'notification' => [
                    'title' => 'Degree imeundwa',
                    'body'  => 'degree ina imeundwa successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'name' => 'Jina',
    ],
];
