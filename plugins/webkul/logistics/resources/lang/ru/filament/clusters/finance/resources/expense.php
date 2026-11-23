<?php

return [
    'navigation' => [
        'title' => 'Расходы',
    ],

    'form' => [
        'sections' => [
            'details'   => 'Расход подробности',
            'reference' => 'Ссылка и приход',
        ],
        'fields' => [
            'company'          => 'Компания',
            'date'             => 'Дата',
            'amount'           => 'Сумма',
            'currency'         => 'Валюта',
            'category'         => 'Категория',
            'paid-by'          => 'Оплачен от',
            'employee'         => 'Сотрудник',
            'payee'            => 'Поставщик или carrier',
            'shipment'         => 'Отправка',
            'trip'             => 'Рейс',
            'vehicle'          => 'Транспорт',
            'vendor-reference' => 'Поставщик ссылка',
            'description'      => 'Описание',
            'receipt'          => 'Приход',
        ],
    ],

    'table' => [
        'columns' => [
            'date'        => 'Дата',
            'category'    => 'Категория',
            'description' => 'Описание',
            'amount'      => 'Сумма',
            'state'       => 'Статус',
            'shipment'    => 'Отправка',
            'company'     => 'Компания',
        ],
        'filters' => [
            'state'    => 'Статус',
            'category' => 'Категория',
        ],
    ],

    'infolist' => [
        'sections' => [
            'summary' => 'Расход',
        ],
        'fields' => [
            'state'       => 'Статус',
            'date'        => 'Дата',
            'amount'      => 'Сумма',
            'category'    => 'Категория',
            'shipment'    => 'Отправка',
            'trip'        => 'Рейс',
            'vehicle'     => 'Транспорт',
            'receipt'     => 'Приход',
            'description' => 'Описание',
        ],
    ],

    'actions' => [
        'submitExpense' => [
            'label'        => 'Отправить',
            'heading'      => 'Отправить этот расход?',
            'notification' => 'Расход submitted.',
        ],
        'approveExpense' => [
            'label'        => 'Утвердить',
            'heading'      => 'Утвердить этот расход?',
            'notification' => 'Расход утверждён.',
        ],
        'rejectExpense' => [
            'label'        => 'Отклонить',
            'heading'      => 'Отклонить этот расход?',
            'notification' => 'Расход отклонён.',
        ],
        'cannot-proceed' => 'Этот расход не ready ещё',
    ],
];
