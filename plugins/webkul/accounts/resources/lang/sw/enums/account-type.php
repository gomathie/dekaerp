<?php

return [
    'assets' => [
        'label'   => 'Mali',
        'options' => [
            'receivable'  => 'Zinazodaiwa',
            'cash'        => 'Benki na fedha taslimu',
            'current'     => 'Mali za muda mfupi',
            'non-current' => 'Mali za muda mrefu',
            'prepayments' => 'Malipo ya awali',
            'fixed'       => 'Mali za kudumu',
        ],
    ],

    'liabilities' => [
        'label'   => 'Madeni',
        'options' => [
            'payable'     => 'Zinazolipwa',
            'credit-card' => 'Kadi ya mkopo',
            'current'     => 'Madeni ya muda mfupi',
            'non-current' => 'Madeni ya muda mrefu',
        ],
    ],

    'equity' => [
        'label'   => 'Mtaji',
        'options' => [
            'equity'     => 'Mtaji',
            'unaffected' => 'Mapato ya mwaka huu',
        ],
    ],

    'income' => [
        'label'   => 'Mapato',
        'options' => [
            'income' => 'Mapato',
            'other'  => 'Mapato mengine',
        ],
    ],

    'expenses' => [
        'label'   => 'Gharama',
        'options' => [
            'expense'      => 'Gharama',
            'depreciation' => 'Uchakavu',
            'direct-cost'  => 'Gharama ya mauzo',
        ],
    ],

    'off-balance' => [
        'label'   => 'Nje ya mizania',
        'options' => [
            'off-balance' => 'Nje ya mizania',
        ],
    ],
];
