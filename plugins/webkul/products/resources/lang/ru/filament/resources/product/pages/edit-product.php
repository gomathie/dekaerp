<?php

return [
    'notification' => [
        'title' => 'Товар обновлено',
        'body'  => 'товар есть обновлено successfully.',
    ],

    'header-actions' => [
        'print' => [
            'label' => 'Печать Labels',

            'form' => [
                'fields' => [
                    'quantity' => 'Номер Labels',
                    'format'   => 'Формат',

                    'format-options' => [
                        'dymo'       => 'Dymo',
                        '2x7_price'  => '2x7 с цена',
                        '4x7_price'  => '4x7 с цена',
                        '4x12'       => '4x12',
                        '4x12_price' => '4x12 с цена',
                    ],
                ],
            ],
        ],

        'delete' => [
            'notification' => [
                'title' => 'Товар удалено',
                'body'  => 'товар есть удалено successfully.',
            ],
        ],
    ],
];
