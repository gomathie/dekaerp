<?php

return [
    'title' => 'Timu',

    'navigation' => [
        'title' => 'Timu',
    ],

    'form' => [
        'fields' => [
            'name' => 'Jina',
        ],
    ],

    'table' => [
        'columns' => [
            'name'       => 'Jina',
            'created-by' => 'Imeundwa na',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Timu imesasishwa',
                    'body'  => 'Timu imesasishwa kwa mafanikio.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Timu imefutwa',
                    'body'  => 'Timu imefutwa kwa mafanikio.',
                ],
            ],
        ],

        'empty-state-actions' => [
            'create' => [
                'notification' => [
                    'title' => 'Timu zimeundwa',
                    'body'  => 'Timu zimeundwa kwa mafanikio.',
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
