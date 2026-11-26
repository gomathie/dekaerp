<?php

$dateFilters = [
    'from'    => 'С',
    'until'   => 'По',
    'between' => 'С :from по :until',
    'since'   => 'С :from',
    'up-to'   => 'По :until',
];

/*
 * Vehicle and driver trip history are the same table with a different filter in
 * front, so they share their column and filter labels rather than keeping two
 * copies to drift apart. TripHistoryExporter labels its file from the
 * vehicle-trips keys whichever page it was started from, which is only correct
 * while the two sets are identical - keep them so.
 */
$tripColumns = [
    'reference' => 'Рейс',
    'vehicle'   => 'Транспортное средство',
    'driver'    => 'Водитель',
    'state'     => 'Статус',
    'started'   => 'Начат',
    'ended'     => 'Завершён',
    'shipments' => 'Отправки',
    'distance'  => 'Расстояние',
];

$tripFilters = $dateFilters + [
    'vehicle' => 'Транспортное средство',
    'driver'  => 'Водитель',
    'state'   => 'Статус',
];

return [
    'export'          => 'Экспорт',
    'export-complete' => 'Экспортировано строк: :count.',

    'shipment-register' => [
        'title'  => 'Реестр отправок',
        'export' => 'Экспорт в Excel',

        'columns' => [
            'reference'   => 'Отправка',
            'customer'    => 'Клиент',
            'state'       => 'Статус',
            'origin'      => 'Откуда',
            'destination' => 'Куда',
            'expected'    => 'Ожидается',
            'delivered'   => 'Доставлено',
            'company'     => 'Компания',
        ],

        'filters' => $dateFilters + [
            'state'    => 'Статус',
            'customer' => 'Клиент',
        ],
    ],

    'delivery-performance' => [
        'title'      => 'Показатели доставки',
        'subheading' => 'Оценивается по ожидаемой дате доставки. Отправки без ожидаемой даты не оцениваются.',
        'export'     => 'Экспорт в Excel',

        'columns' => [
            'reference' => 'Отправка',
            'customer'  => 'Клиент',
            'expected'  => 'Ожидается',
            'delivered' => 'Доставлено',
            'outcome'   => 'Результат',
            'delay'     => 'Опоздание',
        ],

        'outcomes' => [
            'on-time'      => 'Вовремя',
            'late'         => 'С опозданием',
            'failed'       => 'Не удалось',
            'overdue'      => 'Просрочено',
            'pending'      => 'В процессе',
            'not-measured' => 'Дата не обещана',
        ],

        'filters' => $dateFilters + [
            'customer'    => 'Клиент',
            'late-only'   => 'Только с опозданием',
            'failed-only' => 'Только неудачные',
        ],
    ],

    'profitability' => [
        'title'      => 'Рентабельность отправок',
        'subheading' => 'Начисления клиенту против утверждённых затрат, каждое в валюте своей отправки. Черновые затраты не учитываются.',
        'export'     => 'Экспорт в Excel',

        'columns' => [
            'reference' => 'Отправка',
            'customer'  => 'Клиент',
            'revenue'   => 'Выручка',
            'costs'     => 'Затраты',
            'margin'    => 'Маржа',
            'currency'  => 'Валюта',
        ],

        'filters' => $dateFilters + [
            'customer'     => 'Клиент',
            'loss-making'  => 'Только убыточные',
        ],
    ],

    'vehicle-trips' => [
        'title'   => 'История рейсов транспорта',
        'export'  => 'Экспорт в Excel',
        'columns' => $tripColumns,
        'filters' => $tripFilters,
    ],

    'driver-trips' => [
        'title'   => 'История рейсов водителей',
        'export'  => 'Экспорт в Excel',
        'columns' => $tripColumns,
        'filters' => $tripFilters,
    ],
];
