<?php

return [
    'navigation' => [
        'title' => 'Hatua',
    ],

    'form' => [
        'fields' => [
            'name' => 'Jina',
            'done' => 'Imekamilika',
        ],
    ],

    'table' => [
        'columns' => [
            'name'       => 'Jina',
            'done'       => 'Imekamilika',
            'created-at' => 'Imeundwa',
        ],

        'groups' => [
            'done'       => 'Imekamilika',
            'created-at' => 'Imeundwa',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Hatua imesasishwa',
                    'body'  => 'hatua ina imesasishwa successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Hatua imefutwa',
                    'body'  => 'hatua ina imefutwa successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Hatua imefutwa',
                    'body'  => 'hatua ina imefutwa successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Jumla Taarifa',

                'entries' => [
                    'name' => 'Jina',
                    'done' => 'Imekamilika',
                ],
            ],
        ],
    ],
];
