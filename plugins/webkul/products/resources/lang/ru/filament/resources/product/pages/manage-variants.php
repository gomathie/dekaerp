<?php

return [
    'title' => 'Варианты',

    'form' => [
        'date'                   => 'Дата',
        'employee'               => 'Сотрудник',
        'description'            => 'Описание',
        'time-spent'             => 'Затраченное время',
        'time-spent-helper-text' => 'Затраченное время в часах (напр. 1.5 часа — это 1 час 30 минут)',
    ],

    'table' => [
        'columns' => [
            'date'                   => 'Дата',
            'employee'               => 'Сотрудник',
            'description'            => 'Описание',
            'time-spent'             => 'Затраченное время',
            'time-spent-on-subtasks' => 'Время на подзадачи',
            'total-time-spent'       => 'Всего затрачено',
            'remaining-time'         => 'Оставшееся время',
            'variant-values'         => 'Значения варианта',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Вариант удалён',
                    'body'  => 'Вариант успешно удалён.',
                ],
            ],

            'view' => [
                'extra-footer-actions' => [
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
                ],
            ],
        ],
    ],
];
