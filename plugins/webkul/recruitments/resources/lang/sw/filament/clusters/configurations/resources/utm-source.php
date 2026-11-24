<?php

return [
    'title' => 'Vyanzo',

    'navigation' => [
        'title' => 'Vyanzo',
        'group' => 'UTM',
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
            'name-placeholder' => 'Weka jina la chanzo',
            'status'           => 'Hali',
        ],
    ],

    'table' => [
        'columns' => [
            'id'         => 'ID',
            'name'       => 'Jina',
            'status'     => 'Hali',
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
                    'title' => 'Chanzo kimesasishwa',
                    'body'  => 'Chanzo kimesasishwa kwa mafanikio.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Chanzo kimefutwa',
                    'body'  => 'Chanzo kimefutwa kwa mafanikio.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Vyanzo vimefutwa',
                    'body'  => 'Vyanzo vimefutwa kwa mafanikio.',
                ],
            ],
        ],

        'empty-state-action' => [
            'create' => [
                'notification' => [
                    'title' => 'Chanzo kimeundwa',
                    'body'  => 'Chanzo kimeundwa kwa mafanikio.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'name' => 'Jina',
    ],
];
