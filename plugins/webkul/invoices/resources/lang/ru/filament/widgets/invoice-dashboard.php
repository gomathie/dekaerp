<?php

return [
    'unknown'           => 'Неизвестно',
    'other'             => 'Прочее',
    'payment-state'     => 'Статус оплаты',
    'customer'          => 'Клиент',
    'vendor'            => 'Поставщик',
    'increase'          => 'Рост на :percent%',
    'decrease'          => 'Снижение на :percent%',
    'no-change'         => 'Без изменений',
    'invoice-stats'     => [
        'heading'    => 'Счета',
        'total'      => 'Всего выставлено',
        'count'      => 'Количество счетов',
        'paid'       => 'Оплаченные счета',
        'unpaid'     => 'Неоплаченные счета',
        'this-month' => 'Этот месяц',
    ],

    'payment-stats'     => [
        'heading'  => 'Полученные платежи',
        'received' => 'Получено',
        'count'    => 'Платежи',
    ],

    'bill-stats'        => [
        'heading'    => 'Счета поставщиков',
        'total'      => 'Всего по счетам поставщиков',
        'count'      => 'Количество счетов поставщиков',
        'paid'       => 'Оплаченные счета поставщиков',
        'unpaid'     => 'Неоплаченные счета поставщиков',
        'this-month' => 'Этот месяц',
    ],

    'revenue-over-time' => [
        'heading'     => 'Выручка по времени',
        'description' => 'Оплаченные счета клиентов по дням, неделям или месяцам',
        'revenue'     => 'Выручка',
    ],

    'invoice-status'    => [
        'heading'     => 'Счета по статусу',
        'description' => 'Сумма счетов по статусу оплаты',
    ],

    'customer-revenue'  => [
        'heading'      => 'Доля выручки по клиентам',
        'total-billed' => 'Всего выставлено',
        'center-label' => 'Итого',
    ],

    'salespersons'      => [
        'heading'      => 'Эффективность менеджеров',
        'total-billed' => 'Всего выставлено',
    ],

    'top-invoices'      => [
        'heading' => 'Крупнейшие счета',
        'columns' => [
            'reference' => 'Номер',
            'date'      => 'Дата счёта',
            'amount'    => 'Итого',
        ],
    ],

    'top-bills'         => [
        'heading' => 'Крупнейшие счета поставщиков',
        'columns' => [
            'reference' => 'Номер счёта поставщика',
            'date'      => 'Дата счёта поставщика',
            'amount'    => 'Итого',
        ],
    ],
];
