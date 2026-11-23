<?php

return [
    'form' => [
        'name'       => 'Jina',
        'short-name' => 'Short Jina',
    ],

    'table' => [
        'columns' => [
            'name'       => 'Jina',
            'short-name' => 'Short Jina',
            'created-at' => 'Imeundwa',
            'updated-at' => 'Imesasishwa',
        ],

        'filters' => [
            'creator' => 'Creator',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Kichwa imesasishwa',
                    'body'  => 'Kichwa ina imesasishwa successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Kichwa imefutwa',
                    'body'  => 'kichwa ina imefutwa successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Titles imefutwa',
                    'body'  => 'titles ina imefutwa successfully.',
                ],
            ],
        ],
    ],
];
