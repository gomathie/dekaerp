<?php

return [
    'navigation' => [
        'title' => 'Производственные заказы',
        'group' => 'Операции',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Общее',
                'fields' => [
                    'product'                => 'Товар',
                    'quantity'               => 'Количество',
                    'uom'                    => 'Ед. изм.',
                    'bill-of-material'       => 'Спецификация',
                    'scheduled-date'         => 'Плановая дата',
                    'scheduled-end'          => 'Плановое окончание',
                    'responsible'            => 'Ответственный',
                    'to-produce'             => 'К производству',
                    'to-produce-placeholder' => 'Предпросмотр изображения',
                    'uom-placeholder'        => 'Ед. изм.',
                ],
            ],
        ],
        'tabs' => [
            'components' => [
                'title'        => 'Компоненты',
                'add-action'   => 'Добавить строку',
                'process-note' => 'Компоненты будут созданы по мере построения производственного процесса.',
                'columns'      => [
                    'component'          => 'Товар',
                    'from'               => 'Откуда',
                    'to-consume'         => 'К расходу',
                    'to-consume-tooltip' => 'Недостаточно доступного количества',
                    'quantity'           => 'Количество',
                    'uom'                => 'Ед. изм.',
                    'forecast'           => 'Прогноз',
                ],
            ],
            'work-orders' => [
                'title'        => 'Рабочие наряды',
                'add-action'   => 'Добавить строку',
                'process-note' => 'Рабочие наряды будут созданы после настройки производственного процесса.',
                'columns'      => [
                    'operation'          => 'Операция',
                    'work-center'        => 'Рабочий центр',
                    'product'            => 'Товар',
                    'quantity-remaining' => 'Осталось',
                    'quantity-produced'  => 'Произведено',
                    'start'              => 'Начало',
                    'end'                => 'Окончание',
                    'expected-duration'  => 'Ожидаемая длительность',
                    'real-duration'      => 'Фактическая длительность',
                    'status'             => 'Статус',
                    'lot-serial'         => 'Партия/серийный номер',
                ],
                'actions'      => [
                    'open-work-order' => [
                        'tooltip' => 'Открыть рабочий наряд',
                    ],

                    'done' => [
                        'label' => 'Выполнено',
                    ],
                ],
            ],
            'by-products' => [
                'title'        => 'Побочная продукция',
                'process-note' => 'Побочная продукция будет создана по мере построения производственного процесса.',
                'columns'      => [
                    'product'    => 'Товар',
                    'to'         => 'Куда',
                    'to-produce' => 'К производству',
                    'uom'        => 'Ед. изм.',
                ],
            ],
            'miscellaneous' => [
                'title'  => 'Прочее',
                'fields' => [
                    'operation-type'             => 'Тип операции',
                    'source'                     => 'Источник',
                    'finished-products-location' => 'Место готовой продукции',
                    'company'                    => 'Компания',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'reference'              => 'Номер',
            'start'                  => 'Начало',
            'end'                    => 'Окончание',
            'deadline'               => 'Крайний срок',
            'product'                => 'Товар',
            'lot-serial-number'      => 'Партия/серийный номер',
            'bill-of-material'       => 'Спецификация',
            'source'                 => 'Источник',
            'responsible'            => 'Ответственный',
            'mo-readiness'           => 'Готовность ПЗ',
            'component-status'       => 'Статус компонентов',
            'quantity'               => 'Количество',
            'uom'                    => 'Ед. изм.',
            'consumption-efficiency' => 'Эффективность расхода',
            'expected-duration'      => 'Ожидаемая длительность',
            'real-duration'          => 'Фактическая длительность',
            'company'                => 'Компания',
            'state'                  => 'Статус',
        ],
        'groups' => [
            'state'            => 'Статус',
            'product'          => 'Товар',
            'bill-of-material' => 'Спецификация',
            'responsible'      => 'Ответственный',
            'deadline'         => 'Крайний срок',
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'   => 'Общее',
                'entries' => [
                    'product'                  => 'Товар',
                    'scheduled-date'           => 'Плановая дата',
                    'responsible'              => 'Ответственный',
                    'quantity'                 => 'Количество',
                    'uom'                      => 'Ед. изм.',
                    'bill-of-material'         => 'Спецификация',
                    'operation-type'           => 'Тип операции',
                    'consumption-efficiency'   => 'Эффективность расхода',
                ],
            ],
        ],
        'tabs' => [
            'components' => [
                'title'        => 'Компоненты',
                'process-note' => 'Компоненты будут доступны после настройки производственного процесса.',
                'columns'      => [
                    'component' => 'Компонент',
                    'quantity'  => 'Количество',
                    'uom'       => 'Ед. изм.',
                ],
            ],
            'work-orders' => [
                'title'        => 'Рабочие наряды',
                'process-note' => 'Рабочие наряды будут доступны после настройки производственного процесса.',
                'columns'      => [
                    'operation'          => 'Операция',
                    'work-center'        => 'Рабочий центр',
                    'product'            => 'Товар',
                    'quantity-remaining' => 'Осталось',
                    'expected-duration'  => 'Ожидаемая длительность',
                    'real-duration'      => 'Фактическая длительность',
                    'lot-serial'         => 'Партия/серийный номер',
                    'start'              => 'Начало',
                    'end'                => 'Окончание',
                ],
            ],
            'by-products' => [
                'title'        => 'Побочная продукция',
                'process-note' => 'Побочная продукция будет доступна после настройки производственного процесса.',
                'columns'      => [
                    'product'    => 'Товар',
                    'to'         => 'Куда',
                    'to-produce' => 'К производству',
                    'uom'        => 'Ед. изм.',
                ],
            ],
            'miscellaneous' => [
                'title'   => 'Прочее',
                'entries' => [
                    'operation-type'             => 'Тип операции',
                    'source'                     => 'Источник',
                    'finished-products-location' => 'Место готовой продукции',
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
                        'title' => 'Производственный заказ подтверждён',
                    ],
                ],

                'cancel' => [
                    'label'        => 'Отменить',
                    'notification' => [
                        'title' => 'Производственный заказ отменён',
                    ],
                ],
            ],
        ],
    ],
];
