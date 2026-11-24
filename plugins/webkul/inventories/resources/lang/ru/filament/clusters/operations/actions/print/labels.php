<?php

return [
    'label' => 'Этикетки',

    'form' => [
        'fields' => [
            'type'          => 'Тип этикеток',
            'quantity'      => 'Количество',
            'format'        => 'Формат',
            'layout'        => 'Макет этикеток',
            'quantity-type' => 'Количество для печати',
            'quantity'      => 'Количество',

            'quantity-type-options' => [
                'operation' => 'Количество в операции',
                'custom'    => 'Своё количество',
                'per-slot'  => 'По одной на партию/СН',
                'per-unit'  => 'По одной на единицу',
            ],

            'type-options' => [
                'product' => 'Этикетки товаров',
                'lot'     => 'Этикетки партий/СН',
            ],

            'format-options' => [
                'dymo'       => 'Dymo',
                '2x7_price'  => '2x7 с ценой',
                '4x7_price'  => '4x7 с ценой',
                '4x12'       => '4x12',
                '4x12_price' => '4x12 с ценой',
            ],
        ],
    ],
];
