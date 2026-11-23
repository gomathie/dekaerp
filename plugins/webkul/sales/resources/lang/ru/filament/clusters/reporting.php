<?php

return [
    'navigation'      => 'Отчёты',
    'export-complete' => 'Ваш продажи экспорт ready: :count строки.',

    'analysis' => [
        'title'      => 'Продажи analysis',
        'subheading' => 'Подтверждён заказы и счета сделано без заказ, в компания валюта. Группа от клиент, salesperson или месяц до see итоги.',
        'export'     => 'Экспорт',
        'sum'        => 'Итого',
        'columns'    => [
            'reference'   => 'Заказ',
            'date'        => 'Дата',
            'customer'    => 'Клиент',
            'salesperson' => 'Salesperson',
            'untaxed'     => 'Без налога',
            'total'       => 'Итого',
        ],
        'groups' => [
            'customer'    => 'Клиент',
            'salesperson' => 'Salesperson',
            'month'       => 'Месяц',
        ],
        'filters' => [
            'from'  => 'От',
            'until' => 'До',
        ],
    ],

    'by-product' => [
        'title'      => 'Продажи от товар',
        'subheading' => 'Строки подтверждён заказы и счета сделано без заказ, в компания валюта. Группа от товар или категория до see quantities и итоги.',
        'export'     => 'Экспорт',
        'sum'        => 'Итого',
        'columns'    => [
            'product'  => 'Товар',
            'category' => 'Категория',
            'order'    => 'Заказ или счёт',
            'date'     => 'Дата',
            'customer' => 'Клиент',
            'quantity' => 'Количество',
            'untaxed'  => 'Без налога',
        ],
        'groups' => [
            'product'  => 'Товар',
            'category' => 'Категория',
        ],
        'filters' => [
            'from'  => 'От',
            'until' => 'До',
        ],
    ],
];
