<?php

return [
    'navigation' => [
        'title' => 'Tipos de serviço',
    ],

    'form' => [
        'fields' => [
            'transport-mode'           => 'Modo de transporte',
            'product-reference'        => 'Referência do produto de serviço',
            'product-reference-helper' => 'Referência do produto de serviço cobrado por este serviço em cada empresa, por exemplo, LOG-FREIGHT.',
        ],
    ],

    'table' => [
        'columns' => [
            'transport-mode'    => 'Modo de transporte',
            'product-reference' => 'Produto de serviço',
        ],
    ],
];
