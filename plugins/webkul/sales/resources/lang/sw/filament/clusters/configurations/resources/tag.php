<?php

return [
    'title' => 'Lebo',

    'navigation' => [
        'title' => 'Lebo',
        'group' => 'Agizo la mauzos',
    ],

    'form' => [
        'fields' => [
            'name'  => 'Jina',
            'color' => 'Rangi',
        ],
    ],

    'table' => [
        'columns' => [
            'created-by' => 'Imeundwa na',
            'name'       => 'Jina',
            'color'      => 'Rangi',
        ],
        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Bidhaa Lebo imesasishwa',
                    'body'  => 'bidhaa Lebo ina imesasishwa successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Bidhaa Lebo imefutwa',
                    'body'  => 'bidhaa Lebo ina imefutwa successfully.',
                ],
            ],
        ],
        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Bidhaa Lebo imefutwa',
                    'body'  => 'bidhaa Lebo ina imefutwa successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'name'  => 'Jina',
            'color' => 'Rangi',
        ],
    ],
];
