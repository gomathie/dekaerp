<?php

return [
    'waiting-time' => [
        // A suggestion only: D14 bills detention when a person confirms it,
        // never automatically.
        'description' => 'Ожидание на точке :stop (:minutes мин сверх бесплатного времени)',
    ],

    'charges' => [
        'title'  => 'Начисления',
        'fields' => [
            'product'            => 'Услуга',
            'description'        => 'Описание',
            'quantity'           => 'Количество',
            'price-unit'         => 'Цена за единицу',
            'discount'           => 'Скидка (%)',
            'taxes'              => 'Налоги',
            'is-billable'        => 'Оплачиваемое',
            'is-billable-helper' => 'При выставлении счёта учитываются только оплачиваемые начисления.',
            'invoiced'           => 'Счёт выставлен',
        ],
    ],

    'invoices' => [
        'title'   => 'Счета',
        'columns' => [
            'number'        => 'Счёт',
            'state'         => 'Статус',
            'payment-state' => 'Платёж',
            'date'          => 'Дата',
            'total'         => 'Итого',
        ],
        'actions' => [
            'open' => 'Открыть в Бухгалтерии',
        ],
    ],

    'unbilled' => [
        'title'   => 'Невыставленные начисления',
        'columns' => [
            'shipment'    => 'Отправка',
            'customer'    => 'Клиент',
            'description' => 'Начисление',
            'quantity'    => 'Кол-во',
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
            'heading'            => 'Выставить счёт по этой отправке?',
            'description'        => 'Каждое оплачиваемое начисление, по которому ещё не выставлен счёт, станет строкой нового счёта клиенту. Уже выставленные начисления не затрагиваются.',
            'notification'       => 'Счёт создан.',
            'notification-body'  => 'Счёт :invoice находится в Бухгалтерии как черновик, который можно проверить перед проведением.',
            'nothing-to-invoice' => 'Нечего выставлять',
        ],
    ],
];
