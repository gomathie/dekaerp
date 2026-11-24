<?php

return [
    'unknown'             => 'Неизвестно',
    'other'               => 'Другое',
    'share'               => 'Share',
    'status'              => 'Статус',
    'delivery-status'     => 'Доставка Статус',
    'billing'             => 'Billing',
    'sales-value'         => 'Подтверждённые заказы и счета, выставленные без заказа',
    'stats'               => [
        'heading'            => 'Key Metrics',
        'total-quotations'   => 'Итого Коммерческие предложения',
        'total-sales-orders' => 'Итого Продажи',
        'total-revenue'      => 'Итого Выручка',
        'average-revenue'    => 'Average Выручка',
        'increase'           => 'increase',
        'decrease'           => 'decrease',
    ],

    'order-activity'      => [
        'heading'     => 'Движение заказов',
        'description' => 'Заказы, подтверждённые за период, по текущему этапу',
        'to-deliver'  => 'К отгрузке',
        'delivered'   => 'Отгружено',
        'to-invoice'  => 'К выставлению счёта',
        'invoiced'    => 'Счёт выставлен',
        'share'       => ':percent% из :total заказов',
    ],

    'monthly-sales'       => [
        'heading'  => 'Ежемесячно Продажи',
        'revenue'  => 'Выручка',
        'previous' => 'Compared период',
    ],

    'revenue-by-category' => [
        'heading' => 'Выручка от Категория',
        'revenue' => 'Выручка',
    ],

    'sales-persons'       => [
        'heading' => 'Продажи Person Performance',
        'revenue' => 'Выручка',
    ],

    'quotation-outcome'   => [
        'heading'     => 'Коммерческие предложения',
        'description' => 'Что became коммерческие предложения сделано в период',
        'won'         => 'Became заказы',
        'open'        => 'Всё ещё открыт',
        'cancelled'   => 'Отменён',
        'count'       => 'Коммерческие предложения',
    ],

    'top-products'        => [
        'heading' => 'Top Товары',
        'columns' => [
            'name'     => 'Товар',
            'quantity' => 'Количество Sold',
            'revenue'  => 'Выручка',
        ],
    ],

    'top-customers'       => [
        'heading' => 'Top Клиенты',
        'columns' => [
            'name'    => 'Клиент',
            'orders'  => 'Продажи',
            'revenue' => 'Выручка',
        ],
    ],

    'top-sales-orders'    => [
        'heading' => 'Top Продажи',
        'columns' => [
            'reference'      => 'Заказ Номер',
            'customer'       => 'Клиент',
            'amount'         => 'Итого Сумма',
            'invoice-status' => 'Счёт Статус',
        ],
    ],

    'top-quotations'      => [
        'heading' => 'Top Коммерческие предложения',
        'columns' => [
            'reference' => 'Ссылка',
            'customer'  => 'Клиент',
            'amount'    => 'Сумма',
        ],
    ],
];
