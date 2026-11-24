<?php

return [
    'form' => [
        'name'       => 'Jina',
        'short-name' => 'Jina fupi',
    ],

    'table' => [
        'columns' => [
            'name'       => 'Jina',
            'short-name' => 'Jina fupi',
            'created-at' => 'Iliundwa',
            'updated-at' => 'Ilisasishwa',
        ],

        'filters' => [
            'creator' => 'Muundaji',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Cheo kimesasishwa',
                    'body'  => 'Cheo kimesasishwa kwa mafanikio.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Cheo kimefutwa',
                    'body'  => 'Cheo kimefutwa kwa mafanikio.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Vyeo vimefutwa',
                    'body'  => 'Vyeo vimefutwa kwa mafanikio.',
                ],
            ],
        ],
    ],
];
