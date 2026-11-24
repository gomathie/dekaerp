<?php

return [
    'title' => 'Lebo',

    'navigation' => [
        'title' => 'Lebo',
        'group' => 'Mfanyakazi',
    ],

    'groups' => [
        'status'     => 'Hali',
        'created-by' => 'Imeundwa na',
        'created-at' => 'Imeundwa',
        'updated-at' => 'Imesasishwa',
    ],

    'form' => [
        'fields' => [
            'name'             => 'Jina',
            'name-placeholder' => 'Weka jina la lebo',
            'color'            => 'Rangi',
        ],
    ],

    'table' => [
        'columns' => [
            'id'         => 'ID',
            'name'       => 'Jina',
            'color'      => 'Rangi',
            'created-by' => 'Imeundwa na',
            'created-at' => 'Imeundwa',
            'updated-at' => 'Imesasishwa',
        ],

        'filters' => [
            'name'       => 'Jina',
            'created-by' => 'Imeundwa na',
            'updated-by' => 'Imesasishwa na',
            'updated-at' => 'Imesasishwa',
            'created-at' => 'Imeundwa',
        ],

        'groups' => [
            'name'         => 'Jina',
            'job-position' => 'Cheo cha kazi',
            'color'        => 'Rangi',
            'created-by'   => 'Imeundwa na',
            'created-at'   => 'Imeundwa',
            'updated-at'   => 'Imesasishwa',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Lebo imesasishwa',
                    'body'  => 'Lebo imesasishwa kikamilifu.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Lebo imefutwa',
                    'body'  => 'Lebo imefutwa kikamilifu.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Lebo zimefutwa',
                    'body'  => 'Lebo zimefutwa kikamilifu.',
                ],
            ],
        ],

        'empty-state-action' => [
            'create' => [
                'notification' => [
                    'title' => 'Lebo imeundwa',
                    'body'  => 'Lebo imeundwa kikamilifu.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'name'  => 'Jina',
        'color' => 'Rangi',
    ],
];
