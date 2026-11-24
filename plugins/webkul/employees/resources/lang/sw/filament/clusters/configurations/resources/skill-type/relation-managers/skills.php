<?php

return [
    'form' => [
        'name' => 'Jina',
    ],

    'table' => [
        'columns' => [
            'name'       => 'Jina',
            'created-at' => 'Imeundwa',
            'updated-at' => 'Imesasishwa',
        ],

        'groups' => [
            'created-at' => 'Imeundwa',
        ],

        'filters' => [
            'deleted-records' => 'Rekodi zilizofutwa',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Ujuzi umesasishwa',
                    'body'  => 'Ujuzi umesasishwa kikamilifu.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Ujuzi umerejeshwa',
                    'body'  => 'Ujuzi umerejeshwa kikamilifu.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Ujuzi umefutwa',
                    'body'  => 'Ujuzi umefutwa kikamilifu.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Ujuzi umefutwa',
                    'body'  => 'Ujuzi umefutwa kikamilifu.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Ujuzi umefutwa kabisa',
                    'body'  => 'Ujuzi umefutwa kabisa.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Ujuzi umerejeshwa',
                    'body'  => 'Ujuzi umerejeshwa kikamilifu.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'name' => 'Jina',
        ],
    ],
];
