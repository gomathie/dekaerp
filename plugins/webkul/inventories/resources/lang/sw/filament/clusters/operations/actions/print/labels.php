<?php

return [
    'label' => 'Lebo',

    'form' => [
        'fields' => [
            'type'          => 'Aina ya lebo',
            'quantity'      => 'Idadi',
            'format'        => 'Muundo',
            'layout'        => 'Mpangilio wa lebo',
            'quantity-type' => 'Idadi ya kuchapisha',
            'quantity'      => 'Idadi',

            'quantity-type-options' => [
                'operation' => 'Idadi ya operesheni',
                'custom'    => 'Idadi maalum',
                'per-slot'  => 'Moja kwa kila kundi/mfululizo',
                'per-unit'  => 'Moja kwa kila kipimo',
            ],

            'type-options' => [
                'product' => 'Lebo za bidhaa',
                'lot'     => 'Lebo za kundi/mfululizo',
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
