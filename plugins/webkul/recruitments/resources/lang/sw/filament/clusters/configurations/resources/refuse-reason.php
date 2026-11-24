<?php

return [
    'title' => 'Sababu ya kukataa',

    'navigation' => [
        'title' => 'Sababu za kukataa',
        'group' => 'Maombi',
    ],

    'form' => [
        'fields' => [
            'name'             => 'Jina',
            'template'         => [
                'title'                    => 'Kiolezo',
                'applicant-refuse'         => 'Kukataliwa kwa mwombaji',
                'applicant-not-interested' => 'Mwombaji hana nia',
            ],
            'name-placeholder' => 'Weka jina la sababu ya kukataa',
        ],
    ],

    'table' => [
        'columns' => [
            'id'         => 'ID',
            'name'       => 'Jina',
            'template'   => 'Kiolezo',
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
                    'title' => 'Sababu ya kukataa imesasishwa',
                    'body'  => 'Sababu ya kukataa imesasishwa kwa mafanikio.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Sababu ya kukataa imefutwa',
                    'body'  => 'Sababu ya kukataa imefutwa kwa mafanikio.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Sababu za kukataa zimefutwa',
                    'body'  => 'Sababu za kukataa zimefutwa kwa mafanikio.',
                ],
            ],
        ],

        'empty-state-action' => [
            'create' => [
                'notification' => [
                    'title' => 'Sababu ya kukataa imeundwa',
                    'body'  => 'Sababu ya kukataa imeundwa kwa mafanikio.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'name'       => 'Jina',
        'template'   => 'Kiolezo',
    ],
];
