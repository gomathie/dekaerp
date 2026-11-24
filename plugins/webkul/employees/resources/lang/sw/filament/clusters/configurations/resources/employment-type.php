<?php

return [
    'title' => 'Aina za ajira',

    'navigation' => [
        'title' => 'Aina za ajira',
        'group' => 'Uajiri',
    ],

    'form' => [
        'fields' => [
            'name'    => 'Aina ya ajira',
            'code'    => 'Msimbo',
            'country' => 'Nchi',
        ],
    ],

    'table' => [
        'columns' => [
            'id'         => 'ID',
            'name'       => 'Aina ya ajira',
            'code'       => 'Msimbo',
            'country'    => 'Nchi',
            'created-by' => 'Imeundwa na',
            'created-at' => 'Imeundwa',
            'updated-at' => 'Imesasishwa',
        ],

        'filters' => [
            'name'       => 'Aina ya ajira',
            'country'    => 'Nchi',
            'created-by' => 'Imeundwa na',
            'updated-at' => 'Imesasishwa',
            'created-at' => 'Imeundwa',
        ],

        'groups' => [
            'name'       => 'Aina ya ajira',
            'country'    => 'Nchi',
            'code'       => 'Msimbo',
            'created-by' => 'Imeundwa na',
            'created-at' => 'Imeundwa',
            'updated-at' => 'Imesasishwa',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Aina ya ajira imehaririwa',
                    'body'  => 'Aina ya ajira imehaririwa kikamilifu.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Aina ya ajira imefutwa',
                    'body'  => 'Aina ya ajira imefutwa kikamilifu.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Aina za ajira zimefutwa',
                    'body'  => 'Aina za ajira zimefutwa kikamilifu.',
                ],
            ],
        ],

        'empty-state-actions' => [
            'create' => [
                'notification' => [
                    'title' => 'Aina ya ajira imeundwa',
                    'body'  => 'Aina ya ajira imeundwa kikamilifu.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'name'    => 'Aina ya ajira',
            'code'    => 'Msimbo',
            'country' => 'Nchi',
        ],
    ],
];
