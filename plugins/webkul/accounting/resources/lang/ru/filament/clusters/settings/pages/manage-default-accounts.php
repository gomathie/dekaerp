<?php

return [
    'title' => 'Manage По умолчанию Счета',

    'setup' => [
        'action'       => 'Задать вверх учёт для этот компания',
        'notice'       => 'Учёт не задать вверх для этот компания ещё. Использовать "Задать вверх учёт" выше до создать его план счетов, журналы и по умолчанию настройки.',
        'notification' => [
            'title' => 'Учёт задать вверх',
            'body'  => 'План счетов, журналы и по умолчанию настройки были создано для этот компания.',
        ],
    ],

    'form' => [
        'exchange-difference-entries' => [
            'label' => 'Exchange Difference Проводки',

            'fields' => [
                'journal' => [
                    'label' => 'Журнал',
                ],

                'gain' => [
                    'label' => 'Gain',
                ],

                'loss' => [
                    'label' => 'Убыток',
                ],
            ],
        ],

        'bank-transfer-and-payments' => [
            'label' => 'Банк Перевод и Оплаты',

            'fields' => [
                'bank-suspense-account' => [
                    'label' => 'Банк Suspense Счёт',
                ],

                'transfer-account' => [
                    'label' => 'Перевод Счёт',
                ],
            ],
        ],

        'product-accounts' => [
            'label' => 'Товар Счета',

            'fields' => [
                'income-account' => [
                    'label' => 'Доход Счёт',
                ],

                'expense-account' => [
                    'label' => 'Расход Счёт',
                ],
            ],
        ],
    ],
];
