<?php

return [
    'navigation' => [
        'title' => 'Tipos de servicio',
    ],

    'form' => [
        'fields' => [
            'transport-mode'           => 'Modo de transporte',
            'product-reference'        => 'Referencia del producto de servicio',
            'product-reference-helper' => 'Referencia del producto de servicio cobrado por este servicio en cada empresa, por ejemplo, LOG-FREIGHT.',
        ],
    ],

    'table' => [
        'columns' => [
            'transport-mode'    => 'Modo de transporte',
            'product-reference' => 'Producto de servicio',
        ],
    ],
];
