<?php

return [
    'title' => 'Shahada',

    'navigation' => [
        'title' => 'Shahada',
        'group' => 'Maombi',
    ],

    'groups' => [
        'status'     => 'Hali',
        'created-by' => 'Imeundwa na',
        'created-at' => 'Tarehe ya kuundwa',
        'updated-at' => 'Tarehe ya kusasishwa',
    ],

    'form' => [
        'fields' => [
            'name'             => 'Jina',
            'name-placeholder' => 'Weka jina la shahada',
        ],
    ],

    'table' => [
        'columns' => [
            'id'         => 'ID',
            'name'       => 'Jina',
            'created-by' => 'Imeundwa na',
            'created-at' => 'Tarehe ya kuundwa',
            'updated-at' => 'Tarehe ya kusasishwa',
        ],

        'filters' => [
            'name'       => 'Jina',
            'employee'   => 'Mfanyakazi',
            'created-by' => 'Imeundwa na',
            'updated-at' => 'Tarehe ya kusasishwa',
            'created-at' => 'Tarehe ya kuundwa',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Shahada imesasishwa',
                    'body'  => 'Shahada imesasishwa kwa mafanikio.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Shahada imefutwa',
                    'body'  => 'Shahada imefutwa kwa mafanikio.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Shahada zimefutwa',
                    'body'  => 'Shahada zimefutwa kwa mafanikio.',
                ],
            ],
        ],

        'empty-state-action' => [
            'create' => [
                'notification' => [
                    'title' => 'Shahada imeundwa',
                    'body'  => 'Shahada imeundwa kwa mafanikio.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'name' => 'Jina',
    ],
];
