<?php

return [
    'label' => 'Печать этикеток',

    'form' => [
        'fields' => [
            'quantity'      => 'Количество',
            'format'        => 'Формат',
            'quantity-type' => 'Количество для печати',

            'quantity-type-options' => [
                'operation' => 'Количество по операции',
                'custom'    => 'Своё количество',
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
