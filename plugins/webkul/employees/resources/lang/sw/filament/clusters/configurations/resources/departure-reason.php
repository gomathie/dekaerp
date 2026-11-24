<?php

return [
    'title' => 'Sababu za kuondoka',

    'navigation' => [
        'title' => 'Sababu za kuondoka',
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
            'name' => 'Jina',
        ],
    ],

    'table' => [
        'columns' => [
            'id'         => 'ID',
            'name'       => 'Jina',
            'created-by' => 'Imeundwa na',
            'created-at' => 'Imeundwa',
            'updated-at' => 'Imesasishwa',
        ],

        'filters' => [
            'name'       => 'Jina',
            'employee'   => 'Mfanyakazi',
            'created-by' => 'Imeundwa na',
            'updated-at' => 'Imesasishwa',
            'created-at' => 'Imeundwa',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Sababu ya kuondoka imesasishwa',
                    'body'  => 'Sababu ya kuondoka imesasishwa kikamilifu.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Sababu ya kuondoka imefutwa',
                    'body'  => 'Sababu ya kuondoka imefutwa kikamilifu.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Sababu za kuondoka zimefutwa',
                    'body'  => 'Sababu za kuondoka zimefutwa kikamilifu.',
                ],
            ],
        ],

        'empty-state-action' => [
            'create' => [
                'notification' => [
                    'title' => 'Sababu ya kuondoka imeundwa',
                    'body'  => 'Sababu ya kuondoka imeundwa kikamilifu.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'name' => 'Jina',
    ],
];
