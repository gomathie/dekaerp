<?php

return [
    'assets' => [
        'label'   => 'Mali',
        'options' => [
            'receivable'  => 'Ya kupokea',
            'cash'        => 'Benki na Taslimu',
            'current'     => 'Sasa Mali',
            'non-current' => 'Non-sasa Mali',
            'prepayments' => 'Prepayments',
            'fixed'       => 'Isiyobadilika Mali',
        ],
    ],

    'liabilities' => [
        'label'   => 'Dhima',
        'options' => [
            'payable'     => 'Ya kulipa',
            'credit-card' => 'Krediti Card',
            'current'     => 'Sasa Dhima',
            'non-current' => 'Non-sasa Dhima',
        ],
    ],

    'equity' => [
        'label'   => 'Mtaji',
        'options' => [
            'equity'     => 'Mtaji',
            'unaffected' => 'Sasa Mwaka Mapato',
        ],
    ],

    'income' => [
        'label'   => 'Mapato',
        'options' => [
            'income' => 'Mapato',
            'other'  => 'Nyingine Mapato',
        ],
    ],

    'expenses' => [
        'label'   => 'Gharama',
        'options' => [
            'expense'      => 'Gharama',
            'depreciation' => 'Depreciation',
            'direct-cost'  => 'Gharama ya Mapato',
        ],
    ],

    'off-balance' => [
        'label'   => 'Zima-Salio Sheet',
        'options' => [
            'off-balance' => 'Zima-Salio Sheet',
        ],
    ],
];
