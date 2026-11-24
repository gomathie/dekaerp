<?php

return [
    'assets' => [
        'label'   => 'Активы',
        'options' => [
            'receivable'  => 'Дебиторская задолженность',
            'cash'        => 'Банк и касса',
            'current'     => 'Оборотные активы',
            'non-current' => 'Внеоборотные активы',
            'prepayments' => 'Авансы выданные',
            'fixed'       => 'Основные средства',
        ],
    ],

    'liabilities' => [
        'label'   => 'Обязательства',
        'options' => [
            'payable'     => 'Кредиторская задолженность',
            'credit-card' => 'Кредитная карта',
            'current'     => 'Краткосрочные обязательства',
            'non-current' => 'Долгосрочные обязательства',
        ],
    ],

    'equity' => [
        'label'   => 'Капитал',
        'options' => [
            'equity'     => 'Капитал',
            'unaffected' => 'Прибыль текущего года',
        ],
    ],

    'income' => [
        'label'   => 'Доходы',
        'options' => [
            'income' => 'Доходы',
            'other'  => 'Прочие доходы',
        ],
    ],

    'expenses' => [
        'label'   => 'Расходы',
        'options' => [
            'expense'      => 'Расходы',
            'depreciation' => 'Амортизация',
            'direct-cost'  => 'Себестоимость продаж',
        ],
    ],

    'off-balance' => [
        'label'   => 'Забалансовые счета',
        'options' => [
            'off-balance' => 'Забалансовые счета',
        ],
    ],
];
