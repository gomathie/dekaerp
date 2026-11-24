<?php

return [
    'unknown'       => 'Неизвестно',
    'other'         => 'Прочее',
    'share'         => 'Доля',
    'status'        => 'Статус',
    'receipt-status'=> 'Статус поступления',
    'stats'         => [
        'heading'              => 'Ключевые показатели',
        'draft-rfqs'           => 'Черновики запросов цены',
        'sent-rfqs'            => 'Отправленные запросы цены',
        'confirmed-orders'     => 'Подтверждённые заказы',
        'total-purchase-value' => 'Общая стоимость закупок',
        'increase'             => 'рост на :percent%',
        'decrease'             => 'снижение на :percent%',
        'no-change'            => 'Без изменений',
        'this-month'           => 'Этот месяц',
    ],

    'trend'        => [
        'heading' => 'Динамика заказов на закупку по статусам',
        'states'  => [
            'draft'    => 'Черновик',
            'sent'     => 'Отправлен',
            'purchase' => 'Закупка',
            'done'     => 'Выполнен',
            'canceled' => 'Отменён',
        ],
    ],

    'vendor-spend' => [
        'heading'         => 'Доля расходов по поставщикам',
        'total-purchased' => 'Всего закуплено',
    ],

    'top-orders'   => [
        'heading' => 'Крупнейшие заказы',
        'columns' => [
            'reference'      => 'Номер заказа',
            'vendor'         => 'Поставщик',
            'billing-status' => 'Статус выставления счетов',
            'amount'         => 'Общая сумма',
        ],
    ],

    'top-products' => [
        'heading' => 'Самые закупаемые товары',
        'columns' => [
            'name'     => 'Товар',
            'quantity' => 'Общее количество',
            'value'    => 'Общая стоимость',
        ],
    ],
    'top-rfqs'     => [
        'heading' => 'Крупнейшие запросы цены',
        'columns' => [
            'reference' => 'Номер запроса цены',
            'status'    => 'Статус',
            'amount'    => 'Общая сумма',
        ],
    ],

    'top-vendors'  => [
        'heading' => 'Основные поставщики',
        'columns' => [
            'vendor' => 'Поставщик',
            'orders' => 'Заказы',
            'amount' => 'Общие расходы',
        ],
    ],
];
