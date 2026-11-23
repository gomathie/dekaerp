<?php

return [
    'title' => 'Agizo Kiolezo Bidhaa',

    'navigation' => [
        'title' => 'Agizo Kiolezo Bidhaa',
        'group' => 'Agizo la mauzos',
    ],

    'global-search' => [
        'name'    => 'Jina',
    ],

    'form' => [
        'fields' => [
            'sort'           => 'Sort',
            'order-template' => 'Agizo Kiolezo',
            'company'        => 'Kampuni',
            'product'        => 'Bidhaa',
            'product-uom'    => 'Bidhaa UOM',
            'creator'        => 'Creator',
            'display-type'   => 'Display Aina',
            'name'           => 'Jina',
            'quantity'       => 'Kiasi',
        ],
    ],

    'table' => [
        'columns' => [
            'sort'           => 'Sort',
            'order-template' => 'Agizo Kiolezo',
            'company'        => 'Kampuni',
            'product'        => 'Bidhaa',
            'product-uom'    => 'Bidhaa UOM',
            'created-by'     => 'Imeundwa Na',
            'display-type'   => 'Display Aina',
            'name'           => 'Jina',
            'quantity'       => 'Kiasi',
            'created-at'     => 'Imeundwa',
            'updated-at'     => 'Imesasishwa',

        ],
        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Agizo Kiolezo Bidhaa imesasishwa',
                    'body'  => 'agizo kiolezo bidhaa ina imesasishwa successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Agizo Kiolezo Bidhaa imefutwa',
                    'body'  => 'agizo kiolezo bidhaa ina imefutwa successfully.',
                ],
            ],
        ],
        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Agizo Kiolezo Bidhaa imefutwa',
                    'body'  => 'agizo kiolezo bidhaa ina imefutwa successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'sort'           => 'Sort Agizo',
            'order-template' => 'Agizo Kiolezo',
            'company'        => 'Kampuni',
            'product'        => 'Bidhaa',
            'product-uom'    => 'Bidhaa UOM',
            'display-type'   => 'Display Aina',
            'name'           => 'Jina',
            'quantity'       => 'Kiasi',
        ],
    ],
];
