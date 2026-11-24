<?php

return [
    'title' => 'Возврат',

    'navigation' => [
        'title' => 'Возвраты',
        'group' => 'Счета',
    ],

    'form' => [
        'section' => [
            'general' => [
                'title'  => 'Общее',
                'fields' => [
                    'vendor-credit-note' => 'Кредит-нота поставщика',
                    'vendor'             => 'Поставщик',
                    'bill-date'          => 'Дата счёта',
                    'bill-reference'     => 'Номер счёта поставщика',
                    'accounting-date'    => 'Дата учёта',
                    'payment-reference'  => 'Назначение платежа',
                    'recipient-bank'     => 'Банк получателя',
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
                            'unit'                => 'Ед. изм.',
                            'taxes'               => 'Налоги',
                            'discount-percentage' => 'Скидка, %',
                            'unit-price'          => 'Цена за ед.',
                            'sub-total'           => 'Подытог',
                        ],
                    ],
                ],
            ],

            'other-information' => [
                'title'    => 'Прочая информация',
                'fieldset' => [
                    'accounting' => [
                        'title' => 'Учёт',

                        'fields' => [
                            'incoterm'          => 'Инкотермс',
                            'incoterm-location' => 'Место по Инкотермс',
                        ],
                    ],

                    'secured' => [
                        'title'  => 'Защищён',
                        'fields' => [
                            'payment-method' => 'Способ оплаты',
                            'auto-post'      => 'Автопроведение',
                            'checked'        => 'Проверен',
                        ],
                    ],

                    'additional-information' => [
                        'title'  => 'Дополнительная информация',
                        'fields' => [
                            'company'  => 'Компания',
                            'currency' => 'Валюта',
                        ],
                    ],
                ],
            ],

            'term-and-conditions' => [
                'title' => 'Условия',
            ],
        ],
    ],

    'infolist' => [
        'section' => [
            'general' => [
                'title'   => 'Общее',
                'entries' => [
                    'vendor-invoice'    => 'Счёт поставщика',
                    'vendor'            => 'Поставщик',
                    'bill-date'         => 'Дата счёта',
                    'bill-reference'    => 'Номер счёта поставщика',
                    'accounting-date'   => 'Дата учёта',
                    'payment-reference' => 'Назначение платежа',
                    'recipient-bank'    => 'Банк получателя',
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
                            'unit'                => 'Ед. изм.',
                            'taxes'               => 'Налоги',
                            'discount-percentage' => 'Скидка, %',
                            'unit-price'          => 'Цена за ед.',
                            'sub-total'           => 'Подытог',
                        ],
                    ],
                ],
            ],

            'other-information' => [
                'title'    => 'Прочая информация',
                'fieldset' => [
                    'accounting' => [
                        'title' => 'Учёт',

                        'entries' => [
                            'incoterm'          => 'Инкотермс',
                            'incoterm-location' => 'Место по Инкотермс',
                        ],
                    ],

                    'secured' => [
                        'title'   => 'Защищён',
                        'entries' => [
                            'payment-method' => 'Способ оплаты',
                            'auto-post'      => 'Автопроведение',
                            'checked'        => 'Проверен',
                        ],
                    ],

                    'additional-information' => [
                        'title'   => 'Дополнительная информация',
                        'entries' => [
                            'company'  => 'Компания',
                            'currency' => 'Валюта',
                        ],
                    ],
                ],
            ],

            'term-and-conditions' => [
                'title' => 'Условия',
            ],
        ],
    ],
];
