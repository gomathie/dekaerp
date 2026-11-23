<?php

$dateFilters = [
    'from'    => 'От',
    'until'   => 'До',
    'between' => 'От :from до :until',
    'since'   => 'От :from',
    'up-to'   => 'Вверх до :until',
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
    'vehicle'   => 'Транспорт',
    'driver'    => 'Водитель',
    'state'     => 'Статус',
    'started'   => 'Started',
    'ended'     => 'Ended',
    'shipments' => 'Отправки',
    'distance'  => 'Distance',
];

$tripFilters = $dateFilters + [
    'vehicle' => 'Транспорт',
    'driver'  => 'Водитель',
    'state'   => 'Статус',
];

return [
    'export'          => 'Экспорт',
    'export-complete' => 'Exported :count строки.',

    'shipment-register' => [
        'title'  => 'Отправка регистрация',
        'export' => 'Экспорт',

        'columns' => [
            'reference'   => 'Отправка',
            'customer'    => 'Клиент',
            'state'       => 'Статус',
            'origin'      => 'От',
            'destination' => 'До',
            'expected'    => 'Ожидается',
            'delivered'   => 'Delivered',
            'company'     => 'Компания',
        ],

        'filters' => $dateFilters + [
            'state'    => 'Статус',
            'customer' => 'Клиент',
        ],
    ],

    'delivery-performance' => [
        'title'      => 'Доставка performance',
        'subheading' => 'Measured против ожидается доставка дата. Отправки с нет ожидается дата не scored.',
        'export'     => 'Экспорт',

        'columns' => [
            'reference' => 'Отправка',
            'customer'  => 'Клиент',
            'expected'  => 'Ожидается',
            'delivered' => 'Delivered',
            'outcome'   => 'Outcome',
            'delay'     => 'Late от',
        ],

        'outcomes' => [
            'on-time'      => 'Вкл. time',
            'late'         => 'Late',
            'failed'       => 'Не удалось',
            'overdue'      => 'Просрочен',
            'pending'      => 'В ход',
            'not-measured' => 'Нет дата promised',
        ],

        'filters' => $dateFilters + [
            'customer'    => 'Клиент',
            'late-only'   => 'Late только',
            'failed-only' => 'Не удалось только',
        ],
    ],

    'profitability' => [
        'title'      => 'Отправка profitability',
        'subheading' => 'Billable charges против утверждён затраты, каждый в отправка’s собственную валюта. Черновик затраты не counted.',
        'export'     => 'Экспорт',

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
            'loss-making'  => 'Убыток-making только',
        ],
    ],

    'vehicle-trips' => [
        'title'   => 'Транспорт рейс история',
        'export'  => 'Экспорт',
        'columns' => $tripColumns,
        'filters' => $tripFilters,
    ],

    'driver-trips' => [
        'title'   => 'Водитель рейс история',
        'export'  => 'Экспорт',
        'columns' => $tripColumns,
        'filters' => $tripFilters,
    ],
];
