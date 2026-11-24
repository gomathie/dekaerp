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
            'created-at' => 'Iliundwa',
        ],

        'groups' => [
            'done'       => 'Imekamilika',
            'created-at' => 'Iliundwa',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Hatua imesasishwa',
                    'body'  => 'Hatua imesasishwa kwa mafanikio.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Hatua imefutwa',
                    'body'  => 'Hatua imefutwa kwa mafanikio.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Hatua zimefutwa',
                    'body'  => 'Hatua zimefutwa kwa mafanikio.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Taarifa za jumla',

                'entries' => [
                    'name' => 'Jina',
                    'done' => 'Imekamilika',
                ],
            ],
        ],
    ],
];
