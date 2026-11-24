<?php

return [
    'navigation' => [
        'title' => 'Hatua muhimu',
    ],

    'form' => [
        'name'         => 'Jina',
        'deadline'     => 'Tarehe ya mwisho',
        'is-completed' => 'Imekamilika',
        'project'      => 'Mradi',
    ],

    'table' => [
        'columns' => [
            'name'         => 'Jina',
            'deadline'     => 'Tarehe ya mwisho',
            'is-completed' => 'Imekamilika',
            'completed-at' => 'Tarehe ya kukamilika',
            'project'      => 'Mradi',
            'creator'      => 'Aliyeunda',
            'created-at'   => 'Tarehe ya kuundwa',
            'updated-at'   => 'Tarehe ya kusasishwa',
        ],

        'groups' => [
            'name'         => 'Jina',
            'is-completed' => 'Imekamilika',
            'project'      => 'Mradi',
            'created-at'   => 'Tarehe ya kuundwa',
        ],

        'filters' => [
            'is-completed' => 'Imekamilika',
            'project'      => 'Mradi',
            'creator'      => 'Aliyeunda',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Hatua muhimu imesasishwa',
                    'body'  => 'Hatua muhimu imesasishwa kwa mafanikio.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Hatua muhimu imefutwa',
                    'body'  => 'Hatua muhimu imefutwa kwa mafanikio.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Hatua muhimu zimefutwa',
                    'body'  => 'Hatua muhimu zimefutwa kwa mafanikio.',
                ],
            ],
        ],
    ],
];
