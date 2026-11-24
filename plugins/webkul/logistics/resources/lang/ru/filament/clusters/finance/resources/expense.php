<?php

return [
    'navigation' => [
        'title' => 'Расходы',
    ],

    'form' => [
        'sections' => [
            'details'   => 'Сведения о расходе',
            'reference' => 'Ссылка и чек',
        ],
        'fields' => [
            'company'          => 'Компания',
            'date'             => 'Дата',
            'amount'           => 'Сумма',
            'currency'         => 'Валюта',
            'category'         => 'Категория',
            'paid-by'          => 'Кем оплачено',
            'employee'         => 'Сотрудник',
            'payee'            => 'Поставщик или перевозчик',
            'shipment'         => 'Отправка',
            'trip'             => 'Рейс',
            'vehicle'          => 'Транспортное средство',
            'vendor-reference' => 'Ссылка поставщика',
            'description'      => 'Описание',
            'receipt'          => 'Чек',
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
            'vehicle'     => 'Транспортное средство',
            'receipt'     => 'Чек',
            'description' => 'Описание',
        ],
    ],

    'actions' => [
        'submitExpense' => [
            'label'        => 'Подать',
            'heading'      => 'Подать этот расход?',
            'notification' => 'Расход подан.',
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
        'cannot-proceed' => 'Этот расход ещё не готов',
    ],
];
