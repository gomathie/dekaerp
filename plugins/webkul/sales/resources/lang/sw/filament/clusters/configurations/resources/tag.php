<?php

return [
    'title' => 'Lebo',

    'navigation' => [
        'title' => 'Lebo',
        'group' => 'Oda za mauzo',
    ],

    'form' => [
        'fields' => [
            'name'  => 'Jina',
            'color' => 'Rangi',
        ],
    ],

    'table' => [
        'columns' => [
            'created-by' => 'Iliundwa na',
            'name'       => 'Jina',
            'color'      => 'Rangi',
        ],
        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Lebo imesasishwa',
                    'body'  => 'Lebo imesasishwa kwa mafanikio.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Lebo imefutwa',
                    'body'  => 'Lebo imefutwa kwa mafanikio.',
                ],
            ],
        ],
        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Lebo imefutwa',
                    'body'  => 'Lebo imefutwa kwa mafanikio.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'name'  => 'Jina',
            'color' => 'Rangi',
        ],
    ],
];
