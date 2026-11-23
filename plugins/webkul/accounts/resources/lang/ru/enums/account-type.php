<?php

return [
    'assets' => [
        'label'   => 'Активы',
        'options' => [
            'receivable'  => 'К получению',
            'cash'        => 'Банк и Касса',
            'current'     => 'Текущий Активы',
            'non-current' => 'Non-текущий Активы',
            'prepayments' => 'Prepayments',
            'fixed'       => 'Фиксированный Активы',
        ],
    ],

    'liabilities' => [
        'label'   => 'Обязательства',
        'options' => [
            'payable'     => 'К оплате',
            'credit-card' => 'Кредит Card',
            'current'     => 'Текущий Обязательства',
            'non-current' => 'Non-текущий Обязательства',
        ],
    ],

    'equity' => [
        'label'   => 'Капитал',
        'options' => [
            'equity'     => 'Капитал',
            'unaffected' => 'Текущий Год Прибыль',
        ],
    ],

    'income' => [
        'label'   => 'Доход',
        'options' => [
            'income' => 'Доход',
            'other'  => 'Другое Доход',
        ],
    ],

    'expenses' => [
        'label'   => 'Расходы',
        'options' => [
            'expense'      => 'Расходы',
            'depreciation' => 'Depreciation',
            'direct-cost'  => 'Стоимость Выручка',
        ],
    ],

    'off-balance' => [
        'label'   => 'Выкл.-Остаток Sheet',
        'options' => [
            'off-balance' => 'Выкл.-Остаток Sheet',
        ],
    ],
];
