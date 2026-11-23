<?php

return [
    'title' => 'Employment Types',

    'navigation' => [
        'title' => 'Employment Types',
        'group' => 'Ajira',
    ],

    'form' => [
        'fields' => [
            'name'    => 'Employment Aina',
            'code'    => 'Msimbo',
            'country' => 'Nchi',
        ],
    ],

    'table' => [
        'columns' => [
            'id'         => 'Kitambulisho',
            'name'       => 'Employment Aina',
            'code'       => 'Msimbo',
            'country'    => 'Nchi',
            'created-by' => 'Imeundwa Na',
            'created-at' => 'Imeundwa',
            'updated-at' => 'Imesasishwa',
        ],

        'filters' => [
            'name'       => 'Employment Aina',
            'country'    => 'Nchi',
            'created-by' => 'Imeundwa Na',
            'updated-at' => 'Imesasishwa',
            'created-at' => 'Imeundwa',
        ],

        'groups' => [
            'name'       => 'Employment Aina',
            'country'    => 'Nchi',
            'code'       => 'Msimbo',
            'created-by' => 'Imeundwa Na',
            'created-at' => 'Imeundwa',
            'updated-at' => 'Imesasishwa',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Employment Aina',
                    'body'  => 'Employment Aina ina edited successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Employment Aina imefutwa',
                    'body'  => 'Employment Aina ina imefutwa successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Employment Types imefutwa',
                    'body'  => 'Employment Types ina imefutwa successfully.',
                ],
            ],
        ],

        'empty-state-actions' => [
            'create' => [
                'notification' => [
                    'title' => 'Employment Types',
                    'body'  => 'Employment Types ina imeundwa successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'name'    => 'Employment Aina',
            'code'    => 'Msimbo',
            'country' => 'Nchi',
        ],
    ],
];
