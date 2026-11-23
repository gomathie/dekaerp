<?php

return [
    'title' => 'Счёт',

    'navigation' => [
        'title' => 'Счета',
        'group' => 'Счета',
    ],

    'form' => [
        'section' => [
            'general' => [
                'title'  => 'Общее',
                'fields' => [
                    'vendor-credit-note' => 'Поставщик Кредит-нота',
                    'vendor'             => 'Поставщик',
                    'bill-date'          => 'Счёт поставщика Дата',
                    'bill-reference'     => 'Счёт поставщика Ссылка',
                    'accounting-date'    => 'Учёт Дата',
                    'payment-reference'  => 'Оплата Ссылка',
                    'recipient-bank'     => 'Получатель Банк',
                    'due-date'           => 'Срок оплаты',
                    'payment-term'       => 'Условие оплаты',
                ],
            ],
        ],

        'tabs' => [
            'invoice-lines' => [
                'title' => 'Строки счёта',

                'repeater' => [
                    'products' => [
                        'title'       => 'Товары',
                        'add-product' => 'Добавить товар',

                        'fields' => [
                            'product'             => 'Товар',
                            'quantity'            => 'Количество',
                            'unit'                => 'Единица',
                            'taxes'               => 'Налоги',
                            'discount-percentage' => 'Скидка',
                            'unit-price'          => 'Цена за единицу',
                            'sub-total'           => 'Промежуточный итог',
                        ],
                    ],
                ],
            ],

            'other-information' => [
                'title'    => 'Другое Сведения',
                'fieldset' => [
                    'accounting' => [
                        'title' => 'Учёт',

                        'fields' => [
                            'incoterm'          => 'Incoterm',
                            'incoterm-location' => 'Incoterm Место',
                        ],
                    ],

                    'secured' => [
                        'title'  => 'Secured',
                        'fields' => [
                            'payment-method' => 'Оплата Method',
                            'auto-post'      => 'Auto Post',
                            'checked'        => 'Checked',
                        ],
                    ],

                    'additional-information' => [
                        'title'  => 'Additional Сведения',
                        'fields' => [
                            'company'  => 'Компания',
                            'currency' => 'Валюта',
                        ],
                    ],
                ],
            ],

            'term-and-conditions' => [
                'title' => 'Term & Conditions',
            ],
        ],
    ],

    'infolist' => [
        'section' => [
            'general' => [
                'title'   => 'Общее',
                'entries' => [
                    'vendor-invoice'    => 'Поставщик Счёт',
                    'vendor'            => 'Поставщик',
                    'bill-date'         => 'Счёт поставщика Дата',
                    'bill-reference'    => 'Счёт поставщика Ссылка',
                    'accounting-date'   => 'Учёт Дата',
                    'payment-reference' => 'Оплата Ссылка',
                    'recipient-bank'    => 'Получатель Банк',
                    'due-date'          => 'Срок оплаты',
                    'payment-term'      => 'Условие оплаты',
                ],
            ],
        ],

        'tabs' => [
            'invoice-lines' => [
                'title' => 'Строки счёта',

                'repeater' => [
                    'products' => [
                        'title'       => 'Товары',
                        'add-product' => 'Добавить товар',

                        'entries' => [
                            'product'             => 'Товар',
                            'quantity'            => 'Количество',
                            'unit'                => 'Единица',
                            'taxes'               => 'Налоги',
                            'discount-percentage' => 'Скидка',
                            'unit-price'          => 'Цена за единицу',
                            'sub-total'           => 'Промежуточный итог',
                        ],
                    ],
                ],
            ],

            'other-information' => [
                'title'    => 'Другое Сведения',
                'fieldset' => [
                    'accounting' => [
                        'title' => 'Учёт',

                        'entries' => [
                            'incoterm'          => 'Incoterm',
                            'incoterm-location' => 'Incoterm Место',
                        ],
                    ],

                    'secured' => [
                        'title'   => 'Secured',
                        'entries' => [
                            'payment-method' => 'Оплата Method',
                            'auto-post'      => 'Auto Post',
                            'checked'        => 'Checked',
                        ],
                    ],

                    'additional-information' => [
                        'title'   => 'Additional Сведения',
                        'entries' => [
                            'company'  => 'Компания',
                            'currency' => 'Валюта',
                        ],
                    ],
                ],
            ],

            'term-and-conditions' => [
                'title' => 'Term & Conditions',
            ],
        ],
    ],
];
