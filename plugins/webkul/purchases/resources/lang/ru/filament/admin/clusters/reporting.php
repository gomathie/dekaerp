<?php

return [
    'navigation'      => 'Отчёты',
    'export-complete' => 'Ваш закупка экспорт ready: :count строки.',

    'analysis' => [
        'title'      => 'Закупка analysis',
        'subheading' => 'Подтверждён заказ на закупкуs в компания валюта. Группа от поставщик, buyer или месяц до see итоги.',
        'export'     => 'Экспорт',
        'sum'        => 'Итого',
        'columns'    => [
            'reference' => 'Заказ',
            'date'      => 'Дата',
            'supplier'  => 'Поставщик',
            'buyer'     => 'Buyer',
            'untaxed'   => 'Без налога',
            'total'     => 'Итого',
        ],
        'groups' => [
            'supplier' => 'Поставщик',
            'buyer'    => 'Buyer',
            'month'    => 'Месяц',
        ],
        'filters' => [
            'from'  => 'От',
            'until' => 'До',
        ],
    ],

    'by-product' => [
        'title'      => 'Закупки от товар',
        'subheading' => 'Строки подтверждён заказ на закупкуs в компания валюта. Группа от товар или категория до see quantities ordered и получено.',
        'export'     => 'Экспорт',
        'sum'        => 'Итого',
        'columns'    => [
            'product'  => 'Товар',
            'category' => 'Категория',
            'order'    => 'Заказ на закупку',
            'date'     => 'Дата',
            'supplier' => 'Поставщик',
            'ordered'  => 'Ordered',
            'received' => 'Получено',
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
