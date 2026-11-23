<?php

return [
    'navigation' => [
        'title' => 'Service Types',
    ],

    'form' => [
        'fields' => [
            'transport-mode'           => 'Transport mode',
            'product-reference'        => 'Service товар ссылка',
            'product-reference-helper' => 'Ссылка service товар charged для этот service в каждый компания, e.g. LOG-FREIGHT.',
        ],
    ],

    'table' => [
        'columns' => [
            'transport-mode'    => 'Transport mode',
            'product-reference' => 'Service товар',
        ],
    ],
];
