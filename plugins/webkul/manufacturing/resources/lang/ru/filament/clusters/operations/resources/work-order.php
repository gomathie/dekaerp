<?php

return [
    'navigation' => [
        'title' => 'Производственный заказs',
        'group' => 'Операции',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Общее',
                'fields' => [
                    'work-order'          => 'Производственный заказ',
                    'work-center'         => 'Work Center',
                    'product'             => 'Товар',
                    'quantity'            => 'Количество',
                    'manufacturing-order' => 'Производство Заказ',
                    'lot-serial'          => 'Партия/Серийный номер Номер',
                    'start-date'          => 'Начать Дата',
                    'end-date'            => 'Конец Дата',
                    'date-range-separator'=> 'до',
                    'expected-duration'   => 'Ожидается Duration',
                    'duration-suffix'     => 'минуты',
                    'real-duration'       => 'Real Duration',
                ],
            ],
        ],
        'tabs' => [
            'time-tracking' => [
                'title'      => 'Time Tracking',
                'add-action' => 'Добавить строка',
                'columns'    => [
                    'user'         => 'Пользователь',
                    'duration'     => 'Duration',
                    'start-date'   => 'Начать Дата',
                    'end-date'     => 'Конец Дата',
                    'productivity' => 'Productivity',
                ],
                'footer' => [
                    'real-duration' => 'Real Duration',
                ],
            ],
            'components' => [
                'title'      => 'Components',
                'add-action' => 'Добавить строка',
                'columns'    => [
                    'product'    => 'Товар',
                    'from'       => 'От',
                    'to-consume' => 'До Consume',
                    'quantity'   => 'Количество',
                    'uom'        => 'UoM',
                ],
            ],
            'work-instruction' => [
                'title'   => 'Work Instruction',
                'entries' => [
                    'operation' => 'Operation',
                    'worksheet' => 'Worksheet',
                ],
            ],
            'blocked-by' => [
                'title'  => 'Blocked От',
                'fields' => [
                    'work-orders' => 'Производственный заказs',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'operation'           => 'Operation',
            'work-center'         => 'Work Center',
            'manufacturing-order' => 'Производство Заказ',
            'product'             => 'Товар',
            'quantity-remaining'  => 'Количество Осталось',
            'lot-serial'          => 'Партия/Серийный номер',
            'start'               => 'Начать',
            'end'                 => 'Конец',
            'expected-duration'   => 'Ожидается Duration',
            'real-duration'       => 'Real Duration',
            'status'              => 'Статус',
        ],
        'groups' => [
            'status'              => 'Статус',
            'work-center'         => 'Work Center',
            'manufacturing-order' => 'Производство Заказ',
            'product'             => 'Товар',
            'start'               => 'Начать',
            'end'                 => 'Конец',
        ],
        'filters' => [
            'work-order'          => 'Производственный заказ',
            'status'              => 'Статус',
            'operation'           => 'Operation',
            'work-center'         => 'Work Center',
            'manufacturing-order' => 'Производство Заказ',
            'product'             => 'Товар',
            'start'               => 'Начать',
            'end'                 => 'Конец',
            'created-at'          => 'Создано',
            'updated-at'          => 'Обновлено',
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'   => 'Общее',
                'entries' => [
                    'work-order'          => 'Производственный заказ',
                    'work-center'         => 'Work Center',
                    'product'             => 'Товар',
                    'quantity'            => 'Количество',
                    'manufacturing-order' => 'Производство Заказ',
                    'lot-serial'          => 'Партия/Серийный номер Номер',
                    'start-date'          => 'Начать Дата',
                    'end-date'            => 'Конец Дата',
                    'expected-duration'   => 'Ожидается Duration',
                    'real-duration'       => 'Real Duration',
                ],
            ],
        ],
        'tabs' => [
            'time-tracking' => [
                'title'  => 'Time Tracking',
                'footer' => [
                    'real-duration' => 'Real Duration',
                ],
            ],
            'components' => [
                'title' => 'Components',
            ],
            'work-instruction' => [
                'title'   => 'Work Instruction',
                'entries' => [
                    'operation' => 'Operation',
                    'worksheet' => 'Worksheet',
                ],
            ],
            'blocked-by' => [
                'title'   => 'Blocked От',
                'columns' => [
                    'work-order'  => 'Производственный заказ',
                    'work-center' => 'Work Center',
                    'status'      => 'Статус',
                ],
            ],
        ],
    ],

    'pages' => [
        'list' => [
            'header-actions' => [
                'create' => [
                    'label' => 'Новое Производственный заказ',
                ],
            ],
        ],
    ],
];
