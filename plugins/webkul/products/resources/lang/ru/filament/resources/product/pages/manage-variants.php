<?php

return [
    'title' => 'Variants',

    'form' => [
        'date'                   => 'Дата',
        'employee'               => 'Сотрудник',
        'description'            => 'Описание',
        'time-spent'             => 'Time Spent',
        'time-spent-helper-text' => 'Time spent в часы (Eg. 1.5 часы means 1 час 30 минуты)',
    ],

    'table' => [
        'columns' => [
            'date'                   => 'Дата',
            'employee'               => 'Сотрудник',
            'description'            => 'Описание',
            'time-spent'             => 'Time Spent',
            'time-spent-on-subtasks' => 'Time Spent вкл. Subtasks',
            'total-time-spent'       => 'Итого Time Spent',
            'remaining-time'         => 'Осталось Time',
            'variant-values'         => 'Variant Values',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Variant удалено',
                    'body'  => 'variant есть удалено successfully.',
                ],
            ],

            'view' => [
                'extra-footer-actions' => [
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
                ],
            ],
        ],
    ],
];
