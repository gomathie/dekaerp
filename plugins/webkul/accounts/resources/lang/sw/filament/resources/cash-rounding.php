<?php

return [
    'form' => [
        'fields' => [
            'name'               => 'Jina',
            'rounding-precision' => 'Rounding Precision',
            'rounding-strategy'  => 'Rounding Strategy',
            'profit-account'     => 'Faida Akaunti',
            'loss-account'       => 'Hasara Akaunti',
            'rounding-method'    => 'Rounding Method',
        ],
    ],

    'table' => [
        'columns' => [
            'name'                 => 'Jina',
            'rounding-strategy'    => 'Rounding Strategy',
            'rounding-method'      => 'Rounding Method',
            'created-by'           => 'Imeundwa Na',
            'profit-account'       => 'Faida Akaunti',
            'loss-account'         => 'Hasara Akaunti',
        ],

        'groups' => [
            'name'              => 'Jina',
            'rounding-strategy' => 'Rounding Strategy',
            'rounding-method'   => 'Rounding Method',
            'created-by'        => 'Imeundwa Na',
            'profit-account'    => 'Faida Akaunti',
            'loss-account'      => 'Hasara Akaunti',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Taslimu Rounding imefutwa',
                    'body'  => 'taslimu rounding ina imefutwa successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Taslimu Rounding imefutwa',
                    'body'  => 'taslimu rounding ina imefutwa successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'name'               => 'Jina',
            'rounding-precision' => 'Rounding Precision',
            'rounding-strategy'  => 'Rounding Strategy',
            'profit-account'     => 'Faida Akaunti',
            'loss-account'       => 'Hasara Akaunti',
            'rounding-method'    => 'Rounding Method',
        ],
    ],
];
