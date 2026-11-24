<?php

return [
    'title' => 'Lebo',

    'navigation' => [
        'title' => 'Lebo',
        'group' => 'Maombi',
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
                    'title' => 'Lebo zimesasishwa',
                    'body'  => 'Lebo zimesasishwa kwa mafanikio.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Lebo zimefutwa',
                    'body'  => 'Lebo zimefutwa kwa mafanikio.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Kategoria za waombaji zimefutwa',
                    'body'  => 'Kategoria za waombaji zimefutwa kwa mafanikio.',
                ],
            ],
        ],

        'empty-state-action' => [
            'create' => [
                'notification' => [
                    'title' => 'Lebo zimeundwa',
                    'body'  => 'Lebo zimeundwa kwa mafanikio.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'name'  => 'Jina',
        'color' => 'Rangi',
    ],
];
