<?php

return [
    'title' => 'Departure Reasons',

    'navigation' => [
        'title' => 'Departure Reasons',
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
            'name' => 'Jina',
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
                    'title' => 'Departure sababu imesasishwa',
                    'body'  => 'departure sababu ina imesasishwa successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Departure sababu imefutwa',
                    'body'  => 'departure sababu ina imefutwa successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Departure reasons imefutwa',
                    'body'  => 'departure reasons ina imefutwa successfully.',
                ],
            ],
        ],

        'empty-state-action' => [
            'create' => [
                'notification' => [
                    'title' => 'Departure sababu imeundwa',
                    'body'  => 'departure sababu ina imeundwa successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'name' => 'Jina',
    ],
];
