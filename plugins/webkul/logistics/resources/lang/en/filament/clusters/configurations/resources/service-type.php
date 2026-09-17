<?php

return [
    'navigation' => [
        'title' => 'Service Types',
    ],

    'form' => [
        'fields' => [
            'transport-mode'           => 'Transport mode',
            'product-reference'        => 'Service product reference',
            'product-reference-helper' => 'Reference of the service product charged for this service in each company, e.g. LOG-FREIGHT.',
        ],
    ],

    'table' => [
        'columns' => [
            'transport-mode'    => 'Transport mode',
            'product-reference' => 'Service product',
        ],
    ],
];
