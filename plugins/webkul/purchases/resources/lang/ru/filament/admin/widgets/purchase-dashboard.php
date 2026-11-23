<?php

return [
    'unknown'       => 'Неизвестно',
    'other'         => 'Другое',
    'share'         => 'Share',
    'status'        => 'Статус',
    'receipt-status'=> 'Приход Статус',
    'stats'         => [
        'heading'              => 'Key Metrics',
        'draft-rfqs'           => 'Черновик RFQs',
        'sent-rfqs'            => 'Отправлено RFQs',
        'confirmed-orders'     => 'Подтверждён Заказы',
        'total-purchase-value' => 'Итого Закупка Значение',
        'increase'             => ':percent% increase',
        'decrease'             => ':percent% decrease',
        'no-change'            => 'Нет изменить',
    ],

    'trend'        => [
        'heading' => 'Заказ на закупкуs Trend от Состояние',
        'states'  => [
            'draft'    => 'Черновик',
            'sent'     => 'Отправлено',
            'purchase' => 'Закупка',
            'done'     => 'Завершён',
            'canceled' => 'Отменён',
        ],
    ],

    'vendor-spend' => [
        'heading'         => 'Поставщик Spend Share',
        'total-purchased' => 'Итого Purchased',
    ],

    'top-orders'   => [
        'heading' => 'Top Заказы',
        'columns' => [
            'reference'      => 'Заказ Номер',
            'vendor'         => 'Поставщик',
            'billing-status' => 'Billing Статус',
            'amount'         => 'Итого Сумма',
        ],
    ],

    'top-products' => [
        'heading' => 'Top Purchased Товары',
        'columns' => [
            'name'     => 'Товар',
            'quantity' => 'Итого Количество',
            'value'    => 'Итого Значение',
        ],
    ],
    'top-rfqs'     => [
        'heading' => 'Top RFQs',
        'columns' => [
            'reference' => 'Запрос котировок Номер',
            'status'    => 'Статус',
            'amount'    => 'Итого Сумма',
        ],
    ],

    'top-vendors'  => [
        'heading' => 'Top Поставщики',
        'columns' => [
            'vendor' => 'Поставщик',
            'orders' => 'Заказы',
            'amount' => 'Итого Spend',
        ],
    ],
];
