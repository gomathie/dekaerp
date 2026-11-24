<?php

return [
    'header-actions' => [
        'print' => [
            'label' => 'Печать этикеток',

            'form' => [
                'fields' => [
                    'quantity' => 'Количество этикеток',
                    'format'   => 'Формат',

                    'format-options' => [
                        'dymo'       => 'Dymo',
                        '2x7_price'  => '2x7 с ценой',
                        '4x7_price'  => '4x7 с ценой',
                        '4x12'       => '4x12',
                        '4x12_price' => '4x12 с ценой',
                    ],
                ],
            ],
        ],

        'delete' => [
            'notification' => [
                'title' => 'Товар удалён',
                'body'  => 'Товар успешно удалён.',
            ],
        ],
    ],
];
