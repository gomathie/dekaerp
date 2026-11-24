<?php

return [
    'unknown'             => 'Неизвестно',
    'other'               => 'Прочее',
    'share'               => 'Доля',
    'status'              => 'Статус',
    'delivery-status'     => 'Статус отгрузки',
    'billing'             => 'Выставление счетов',
    'sales-value'         => 'Подтверждённые заказы и счета без заказа',
    'stats'               => [
        'heading'            => 'Ключевые показатели',
        'total-quotations'   => 'Всего предложений',
        'total-sales-orders' => 'Всего продаж',
        'total-revenue'      => 'Общая выручка',
        'average-revenue'    => 'Средняя выручка',
        'increase'           => 'рост',
        'decrease'           => 'снижение',
        'this-month'         => 'Этот месяц',
    ],

    'order-activity'      => [
        'heading'     => 'Активность заказов',
        'description' => 'Заказы, подтверждённые за период, по текущему этапу',
        'to-deliver'  => 'К отгрузке',
        'delivered'   => 'Отгружено',
        'to-invoice'  => 'К выставлению',
        'invoiced'    => 'Выставлено',
        'share'       => ':percent% из :total заказов',
    ],

    'monthly-sales'       => [
        'heading'  => 'Продажи по времени',
        'revenue'  => 'Выручка',
        'previous' => 'Период сравнения',
    ],

    'revenue-by-category' => [
        'heading' => 'Выручка по категориям',
        'revenue' => 'Выручка',
    ],

    'sales-persons'       => [
        'heading' => 'Эффективность менеджеров',
        'revenue' => 'Выручка',
    ],

    'quotation-outcome'   => [
        'heading'     => 'Предложения',
        'description' => 'Что стало с предложениями, созданными за период',
        'won'         => 'Стали заказами',
        'open'        => 'Ещё открыты',
        'cancelled'   => 'Отменены',
        'count'       => 'Предложения',
    ],

    'top-products'        => [
        'heading' => 'Лучшие товары',
        'columns' => [
            'name'     => 'Товар',
            'quantity' => 'Продано',
            'revenue'  => 'Выручка',
        ],
    ],

    'top-customers'       => [
        'heading' => 'Лучшие клиенты',
        'columns' => [
            'name'    => 'Клиент',
            'orders'  => 'Продажи',
            'revenue' => 'Выручка',
        ],
    ],

    'top-sales-orders'    => [
        'heading' => 'Крупнейшие продажи',
        'columns' => [
            'reference'      => 'Номер заказа',
            'customer'       => 'Клиент',
            'amount'         => 'Итоговая сумма',
            'invoice-status' => 'Статус выставления',
        ],
    ],

    'top-quotations'      => [
        'heading' => 'Крупнейшие предложения',
        'columns' => [
            'reference' => 'Референс',
            'customer'  => 'Клиент',
            'amount'    => 'Сумма',
        ],
    ],
];
