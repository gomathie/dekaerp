<?php

return [
    'label' => 'Labels',

    'form' => [
        'fields' => [
            'type'          => 'Тип Labels',
            'quantity'      => 'Количество',
            'format'        => 'Формат',
            'layout'        => 'Layout Labels',
            'quantity-type' => 'Количество До Печать',
            'quantity'      => 'Количество',

            'quantity-type-options' => [
                'operation' => 'Operation Количество',
                'custom'    => 'Свой Количество',
                'per-slot'  => 'One за партия/SN',
                'per-unit'  => 'One за единица',
            ],

            'type-options' => [
                'product' => 'Товар Labels',
                'lot'     => 'Партия/SN Labels',
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
