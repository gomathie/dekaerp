<?php

return [
    'navigation' => [
        'title' => 'Производство Заказы',
        'group' => 'Операции',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Общее',
                'fields' => [
                    'product'                => 'Товар',
                    'quantity'               => 'Количество',
                    'uom'                    => 'UoM',
                    'bill-of-material'       => 'Счёт поставщика Material',
                    'scheduled-date'         => 'Scheduled Дата',
                    'scheduled-end'          => 'Scheduled Конец',
                    'responsible'            => 'Responsible',
                    'to-produce'             => 'До Produce',
                    'to-produce-placeholder' => 'Изображение preview',
                    'uom-placeholder'        => 'UoM',
                ],
            ],
        ],
        'tabs' => [
            'components' => [
                'title'        => 'Components',
                'add-action'   => 'Добавить строка',
                'process-note' => 'Components быть generated как производство process built.',
                'columns'      => [
                    'component'          => 'Товар',
                    'from'               => 'От',
                    'to-consume'         => 'До Consume',
                    'to-consume-tooltip' => 'Insufficient количество доступно',
                    'quantity'           => 'Количество',
                    'uom'                => 'UoM',
                    'forecast'           => 'Прогноз',
                ],
            ],
            'work-orders' => [
                'title'        => 'Производственный заказs',
                'add-action'   => 'Добавить строка',
                'process-note' => 'Производственный заказs быть generated после производство process configured.',
                'columns'      => [
                    'operation'          => 'Operation',
                    'work-center'        => 'Work Center',
                    'product'            => 'Товар',
                    'quantity-remaining' => 'Количество Осталось',
                    'quantity-produced'  => 'Количество Produced',
                    'start'              => 'Начать',
                    'end'                => 'Конец',
                    'expected-duration'  => 'Ожидается Duration',
                    'real-duration'      => 'Real Duration',
                    'status'             => 'Статус',
                    'lot-serial'         => 'Партия/Серийный номер',
                ],
                'actions'      => [
                    'open-work-order' => [
                        'tooltip' => 'Открыт производственный заказ',
                    ],

                    'done' => [
                        'label' => 'Завершён',
                    ],
                ],
            ],
            'by-products' => [
                'title'        => 'От-Товары',
                'process-note' => 'От-товары быть generated как производство process built.',
                'columns'      => [
                    'product'    => 'Товар',
                    'to'         => 'До',
                    'to-produce' => 'До Produce',
                    'uom'        => 'UoM',
                ],
            ],
            'miscellaneous' => [
                'title'  => 'Miscellaneous',
                'fields' => [
                    'operation-type'             => 'Operation Тип',
                    'source'                     => 'Источник',
                    'finished-products-location' => 'Finished Товары Место',
                    'company'                    => 'Компания',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'reference'              => 'Ссылка',
            'start'                  => 'Начать',
            'end'                    => 'Конец',
            'deadline'               => 'Deadline',
            'product'                => 'Товар',
            'lot-serial-number'      => 'Партия/Серийный номер Номер',
            'bill-of-material'       => 'Счёт поставщика Material',
            'source'                 => 'Источник',
            'responsible'            => 'Responsible',
            'mo-readiness'           => 'MO Readiness',
            'component-status'       => 'Component Статус',
            'quantity'               => 'Количество',
            'uom'                    => 'UoM',
            'consumption-efficiency' => 'Consumption Efficiency',
            'expected-duration'      => 'Ожидается Duration',
            'real-duration'          => 'Real Duration',
            'company'                => 'Компания',
            'state'                  => 'Состояние',
        ],
        'groups' => [
            'state'            => 'Состояние',
            'product'          => 'Товар',
            'bill-of-material' => 'Счёт поставщика Material',
            'responsible'      => 'Responsible',
            'deadline'         => 'Deadline',
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'   => 'Общее',
                'entries' => [
                    'product'                  => 'Товар',
                    'scheduled-date'           => 'Scheduled Дата',
                    'responsible'              => 'Responsible',
                    'quantity'                 => 'Количество',
                    'uom'                      => 'UoM',
                    'bill-of-material'         => 'Счёт поставщика Material',
                    'operation-type'           => 'Operation Тип',
                    'consumption-efficiency'   => 'Consumption Efficiency',
                ],
            ],
        ],
        'tabs' => [
            'components' => [
                'title'        => 'Components',
                'process-note' => 'Components быть доступно после производство process configured.',
                'columns'      => [
                    'component' => 'Component',
                    'quantity'  => 'Количество',
                    'uom'       => 'UoM',
                ],
            ],
            'work-orders' => [
                'title'        => 'Производственный заказs',
                'process-note' => 'Производственный заказs быть доступно после производство process configured.',
                'columns'      => [
                    'operation'          => 'Operation',
                    'work-center'        => 'Work Center',
                    'product'            => 'Товар',
                    'quantity-remaining' => 'Количество Осталось',
                    'expected-duration'  => 'Ожидается Duration',
                    'real-duration'      => 'Real Duration',
                    'lot-serial'         => 'Партия/Серийный номер',
                    'start'              => 'Начать',
                    'end'                => 'Конец',
                ],
            ],
            'by-products' => [
                'title'        => 'От-Товары',
                'process-note' => 'От-товары быть доступно после производство process configured.',
                'columns'      => [
                    'product'    => 'Товар',
                    'to'         => 'До',
                    'to-produce' => 'До Produce',
                    'uom'        => 'UoM',
                ],
            ],
            'miscellaneous' => [
                'title'   => 'Miscellaneous',
                'entries' => [
                    'operation-type'             => 'Operation Тип',
                    'source'                     => 'Источник',
                    'finished-products-location' => 'Finished Товары Место',
                    'company'                    => 'Компания',
                ],
            ],
        ],
    ],

    'pages' => [
        'shared' => [
            'header-actions' => [
                'confirm' => [
                    'label'        => 'Подтвердить',
                    'notification' => [
                        'title' => 'Производство заказ подтверждён',
                    ],
                ],

                'cancel' => [
                    'label'        => 'Отмена',
                    'notification' => [
                        'title' => 'Производство заказ отменён',
                    ],
                ],
            ],
        ],
    ],
];
