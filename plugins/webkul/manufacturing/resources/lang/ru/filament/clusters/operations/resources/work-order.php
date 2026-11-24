<?php

return [
    'navigation' => [
        'title' => 'Рабочие наряды',
        'group' => 'Операции',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Общее',
                'fields' => [
                    'work-order'          => 'Рабочий наряд',
                    'work-center'         => 'Рабочий центр',
                    'product'             => 'Товар',
                    'quantity'            => 'Количество',
                    'manufacturing-order' => 'Производственный заказ',
                    'lot-serial'          => 'Партия/серийный номер',
                    'start-date'          => 'Дата начала',
                    'end-date'            => 'Дата окончания',
                    'date-range-separator'=> 'по',
                    'expected-duration'   => 'Ожидаемая длительность',
                    'duration-suffix'     => 'минут',
                    'real-duration'       => 'Фактическая длительность',
                ],
            ],
        ],
        'tabs' => [
            'time-tracking' => [
                'title'      => 'Учёт времени',
                'add-action' => 'Добавить строку',
                'columns'    => [
                    'user'         => 'Пользователь',
                    'duration'     => 'Длительность',
                    'start-date'   => 'Дата начала',
                    'end-date'     => 'Дата окончания',
                    'productivity' => 'Производительность',
                ],
                'footer' => [
                    'real-duration' => 'Фактическая длительность',
                ],
            ],
            'components' => [
                'title'      => 'Компоненты',
                'add-action' => 'Добавить строку',
                'columns'    => [
                    'product'    => 'Товар',
                    'from'       => 'Откуда',
                    'to-consume' => 'К расходу',
                    'quantity'   => 'Количество',
                    'uom'        => 'Ед. изм.',
                ],
            ],
            'work-instruction' => [
                'title'   => 'Рабочая инструкция',
                'entries' => [
                    'operation' => 'Операция',
                    'worksheet' => 'Инструкция',
                ],
            ],
            'blocked-by' => [
                'title'  => 'Заблокирован нарядами',
                'fields' => [
                    'work-orders' => 'Рабочие наряды',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'operation'           => 'Операция',
            'work-center'         => 'Рабочий центр',
            'manufacturing-order' => 'Производственный заказ',
            'product'             => 'Товар',
            'quantity-remaining'  => 'Осталось',
            'lot-serial'          => 'Партия/серийный номер',
            'start'               => 'Начало',
            'end'                 => 'Окончание',
            'expected-duration'   => 'Ожидаемая длительность',
            'real-duration'       => 'Фактическая длительность',
            'status'              => 'Статус',
        ],
        'groups' => [
            'status'              => 'Статус',
            'work-center'         => 'Рабочий центр',
            'manufacturing-order' => 'Производственный заказ',
            'product'             => 'Товар',
            'start'               => 'Начало',
            'end'                 => 'Окончание',
        ],
        'filters' => [
            'work-order'          => 'Рабочий наряд',
            'status'              => 'Статус',
            'operation'           => 'Операция',
            'work-center'         => 'Рабочий центр',
            'manufacturing-order' => 'Производственный заказ',
            'product'             => 'Товар',
            'start'               => 'Начало',
            'end'                 => 'Окончание',
            'created-at'          => 'Создан',
            'updated-at'          => 'Обновлён',
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'   => 'Общее',
                'entries' => [
                    'work-order'          => 'Рабочий наряд',
                    'work-center'         => 'Рабочий центр',
                    'product'             => 'Товар',
                    'quantity'            => 'Количество',
                    'manufacturing-order' => 'Производственный заказ',
                    'lot-serial'          => 'Партия/серийный номер',
                    'start-date'          => 'Дата начала',
                    'end-date'            => 'Дата окончания',
                    'expected-duration'   => 'Ожидаемая длительность',
                    'real-duration'       => 'Фактическая длительность',
                ],
            ],
        ],
        'tabs' => [
            'time-tracking' => [
                'title'  => 'Учёт времени',
                'footer' => [
                    'real-duration' => 'Фактическая длительность',
                ],
            ],
            'components' => [
                'title' => 'Компоненты',
            ],
            'work-instruction' => [
                'title'   => 'Рабочая инструкция',
                'entries' => [
                    'operation' => 'Операция',
                    'worksheet' => 'Инструкция',
                ],
            ],
            'blocked-by' => [
                'title'   => 'Заблокирован нарядами',
                'columns' => [
                    'work-order'  => 'Рабочий наряд',
                    'work-center' => 'Рабочий центр',
                    'status'      => 'Статус',
                ],
            ],
        ],
    ],

    'pages' => [
        'list' => [
            'header-actions' => [
                'create' => [
                    'label' => 'Новый рабочий наряд',
                ],
            ],
        ],
    ],
];
