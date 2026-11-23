<?php

return [
    'title' => 'Sources',

    'navigation' => [
        'title' => 'Sources',
        'group' => 'UTMs',
    ],

    'groups' => [
        'status'     => 'Hali',
        'created-by' => 'Imeundwa Na',
        'created-at' => 'Imeundwa',
        'updated-at' => 'Imesasishwa',
    ],

    'form' => [
        'fields' => [
            'name'             => 'Jina',
            'name-placeholder' => 'Ingiza jina ya chanzo',
            'status'           => 'Hali',
        ],
    ],

    'table' => [
        'columns' => [
            'id'         => 'Kitambulisho',
            'name'       => 'Jina',
            'status'     => 'Hali',
            'created-by' => 'Imeundwa Na',
            'created-at' => 'Imeundwa',
            'updated-at' => 'Imesasishwa',
        ],

        'filters' => [
            'name'       => 'Jina',
            'created-by' => 'Imeundwa Na',
            'updated-at' => 'Imesasishwa',
            'created-at' => 'Imeundwa',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Chanzo imesasishwa',
                    'body'  => 'chanzo ina imesasishwa successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Chanzo imefutwa',
                    'body'  => 'chanzo ina imefutwa successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Sources imefutwa',
                    'body'  => 'Sources ina imefutwa successfully.',
                ],
            ],
        ],

        'empty-state-action' => [
            'create' => [
                'notification' => [
                    'title' => 'Chanzo imeundwa',
                    'body'  => 'chanzo ina imeundwa successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'name' => 'Jina',
    ],
];
