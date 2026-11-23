<?php

return [
    'label' => 'Labels',

    'form' => [
        'fields' => [
            'type'          => 'Aina Ya Labels',
            'quantity'      => 'Kiasi',
            'format'        => 'Umbizo',
            'layout'        => 'Layout Ya Labels',
            'quantity-type' => 'Kiasi Hadi Chapisha',
            'quantity'      => 'Kiasi',

            'quantity-type-options' => [
                'operation' => 'Operation Kiasi',
                'custom'    => 'Maalum Kiasi',
                'per-slot'  => 'One kwa kundi/SN',
                'per-unit'  => 'One kwa uniti',
            ],

            'type-options' => [
                'product' => 'Bidhaa Labels',
                'lot'     => 'Kundi/SN Labels',
            ],

            'format-options' => [
                'dymo'       => 'Dymo',
                '2x7_price'  => '2x7 na bei',
                '4x7_price'  => '4x7 na bei',
                '4x12'       => '4x12',
                '4x12_price' => '4x12 na bei',
            ],
        ],
    ],
];
