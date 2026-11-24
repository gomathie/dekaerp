<?php

return [
    'title' => 'Njia',

    'navigation' => [
        'title' => 'Njia',
        'group' => 'UTM',
    ],

    'form' => [
        'fields' => [
            'name'             => 'Jina',
            'name-placeholder' => 'Weka jina la njia',
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
            'created-by' => 'Imeundwa na',
            'updated-at' => 'Tarehe ya kusasishwa',
            'created-at' => 'Tarehe ya kuundwa',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Njia imesasishwa',
                    'body'  => 'Njia imesasishwa kwa mafanikio.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Njia imefutwa',
                    'body'  => 'Njia imefutwa kwa mafanikio.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Njia zimefutwa',
                    'body'  => 'Njia zimefutwa kwa mafanikio.',
                ],
            ],
        ],

        'empty-state-action' => [
            'create' => [
                'notification' => [
                    'title' => 'Njia imeundwa',
                    'body'  => 'Njia imeundwa kwa mafanikio.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'name' => 'Jina',
    ],
];
