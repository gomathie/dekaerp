<?php

return [
    'label' => 'Return',

    'modal' => [
        'form' => [
            'columns' => [
                'product'                 => 'Bidhaa',
                'quantity'                => 'Kiasi',
                'uom'                     => 'UOM',
                'excess-quantity-tooltip' => 'kiasi hadi return ni greater kuliko kiasi processed katika original operation.',
            ],
        ],
    ],

    'notification' => [
        'no-products' => [
            'body' => 'Hakuna bidhaa hadi return (tu mistari katika Imekamilika hali na si fully returned bado unaweza kuwa returned).',
        ],
        'no-quantities' => [
            'body' => 'Tafadhali specify saa kidogo one non-zero kiasi.',
        ],
    ],
];
