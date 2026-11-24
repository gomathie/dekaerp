<?php

return [
    'navigation' => [
        'title' => 'Kategoria',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Taarifa za jumla',

                'fields' => [
                    'name'       => 'Jina',
                    'technician' => 'Mwajibikaji',
                    'company'    => 'Kampuni',
                    'note'       => 'Kidokezo',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'       => 'Jina',
            'technician' => 'Mwajibikaji',
            'company'    => 'Kampuni',
            'created-at' => 'Iliundwa',
        ],

        'groups' => [
            'technician' => 'Mwajibikaji',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Kategoria imesasishwa',
                    'body'  => 'Kategoria imesasishwa kwa mafanikio.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Kategoria imefutwa',
                    'body'  => 'Kategoria imefutwa kwa mafanikio.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Kategoria zimefutwa',
                    'body'  => 'Kategoria zimefutwa kwa mafanikio.',
                ],
            ],
        ],

        'empty-state' => [
            'create' => [
                'notification' => [
                    'title' => 'Kategoria imeundwa',
                    'body'  => 'Kategoria imeundwa kwa mafanikio.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Taarifa za jumla',

                'entries' => [
                    'name'       => 'Jina',
                    'technician' => 'Mwajibikaji',
                    'company'    => 'Kampuni',
                    'note'       => 'Kidokezo',
                ],
            ],
        ],
    ],
];
