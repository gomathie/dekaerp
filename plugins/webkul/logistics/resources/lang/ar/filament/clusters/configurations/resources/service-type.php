<?php

return [
    'navigation' => [
        'title' => 'أنواع الخدمات',
    ],

    'form' => [
        'fields' => [
            'transport-mode'           => 'وسيلة النقل',
            'product-reference'        => 'مرجع منتج الخدمة',
            'product-reference-helper' => 'مرجع منتج الخدمة الذي تُحتسب تكلفته لهذه الخدمة في كل شركة، مثل LOG-FREIGHT.',
        ],
    ],

    'table' => [
        'columns' => [
            'transport-mode'    => 'وسيلة النقل',
            'product-reference' => 'منتج الخدمة',
        ],
    ],
];
