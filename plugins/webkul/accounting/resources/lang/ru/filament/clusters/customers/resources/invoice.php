<?php

return [
    'title' => 'Счёт',

    'navigation' => [
        'title' => 'Счета',
    ],

    'global-search' => [
        'customer' => 'Клиент',
        'date'     => 'Дата',
        'due-date' => 'Срок оплаты',
        'amount'   => 'Сумма',
    ],

    'form' => [
        'tabs' => [
            'invoice-lines' => [
                'repeater' => [
                    'products' => [
                        'actions' => [
                            'open-product' => [
                                'tooltip' => 'Открыть товар',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
];
