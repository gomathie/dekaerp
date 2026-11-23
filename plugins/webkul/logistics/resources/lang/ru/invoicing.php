<?php

return [
    'waiting-time' => [
        // A suggestion only: D14 bills detention when a person confirms it,
        // never automatically.
        'description' => 'Waiting time в :stop (:minutes min более free allowance)',
    ],

    'charges' => [
        'title'  => 'Charges',
        'fields' => [
            'product'            => 'Service',
            'description'        => 'Описание',
            'quantity'           => 'Количество',
            'price-unit'         => 'Цена за единицу',
            'discount'           => 'Скидка (%)',
            'taxes'              => 'Налоги',
            'is-billable'        => 'Billable',
            'is-billable-helper' => 'Только billable charges picked вверх когда счёт raised.',
            'invoiced'           => 'Invoiced',
        ],
    ],

    'invoices' => [
        'title'   => 'Счета',
        'columns' => [
            'number'        => 'Счёт',
            'state'         => 'Статус',
            'payment-state' => 'Оплата',
            'date'          => 'Дата',
            'total'         => 'Итого',
        ],
        'actions' => [
            'open' => 'Открыт в Учёт',
        ],
    ],

    'unbilled' => [
        'title'   => 'Unbilled charges',
        'columns' => [
            'shipment'    => 'Отправка',
            'customer'    => 'Клиент',
            'description' => 'Charge',
            'quantity'    => 'Qty',
            'subtotal'    => 'Сумма',
            'total'       => 'Итого',
        ],
        'filters' => [
            'currency' => 'Валюта',
            'shipment' => 'Отправка',
        ],
    ],

    'actions' => [
        'create-invoice' => [
            'label'              => 'Создать счёт',
            'heading'            => 'Счёт этот отправка?',
            'description'        => 'Каждый billable charge тот есть не invoiced ещё becomes строка вкл. новое Счёт клиенту. Charges уже invoiced осталось alone.',
            'notification'       => 'Счёт создано.',
            'notification-body'  => 'Счёт :invoice в Учёт, как черновик вы можно review до posting.',
            'nothing-to-invoice' => 'Nothing до счёт',
        ],
    ],
];
