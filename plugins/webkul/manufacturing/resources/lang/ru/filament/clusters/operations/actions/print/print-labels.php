<?php

return [
    'label' => 'Печать Labels',

    'form' => [
        'fields' => [
            'quantity'      => 'Количество',
            'format'        => 'Формат',
            'quantity-type' => 'Количество До Печать',

            'quantity-type-options' => [
                'operation' => 'Operation Количество',
                'custom'    => 'Свой Количество',
            ],

            'format-options' => [
                'dymo'       => 'Dymo',
                '2x7_price'  => '2x7 с цена',
                '4x7_price'  => '4x7 с цена',
                '4x12'       => '4x12',
                '4x12_price' => '4x12 с цена',
            ],
        ],
    ],
];
