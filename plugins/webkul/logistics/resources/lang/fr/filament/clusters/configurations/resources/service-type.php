<?php

return [
    'navigation' => [
        'title' => 'Types de service',
    ],

    'form' => [
        'fields' => [
            'transport-mode'           => 'Mode de transport',
            'product-reference'        => 'Référence du produit de service',
            'product-reference-helper' => 'Référence du produit de service facturé pour ce service dans chaque société, par exemple LOG-FREIGHT.',
        ],
    ],

    'table' => [
        'columns' => [
            'transport-mode'    => 'Mode de transport',
            'product-reference' => 'Produit de service',
        ],
    ],
];
