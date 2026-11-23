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
                    'customer-invoice' => 'Клиент Кредит-нота',
                    'customer'         => 'Клиент',
                    'invoice-date'     => 'Дата счёта',
                    'due-date'         => 'Срок оплаты',
                    'payment-term'     => 'Условие оплаты',
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
                    'invoice' => [
                        'title'  => 'Счёт',
                        'fields' => [
                            'customer-reference' => 'Ссылка клиента',
                            'sales-person'       => 'Продажи Person',
                            'payment-reference'  => 'Оплата Ссылка',
                            'recipient-bank'     => 'Получатель Банк',
                            'delivery-date'      => 'Доставка Дата',
                        ],
                    ],

                    'accounting' => [
                        'title' => 'Учёт',

                        'fields' => [
                            'incoterm'          => 'Incoterm',
                            'incoterm-location' => 'Incoterm Место',
                            'payment-method'    => 'Оплата Method',
                            'auto-post'         => 'Auto Post',
                            'checked'           => 'Checked',
                        ],
                    ],

                    'additional-information' => [
                        'title'  => 'Additional Сведения',
                        'fields' => [
                            'company'  => 'Компания',
                            'currency' => 'Валюта',
                        ],
                    ],

                    'marketing' => [
                        'title'  => 'Marketing',
                        'fields' => [
                            'campaign' => 'Campaign',
                            'medium'   => 'Средний',
                            'source'   => 'Источник',
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
                    'customer-invoice' => 'Клиент Кредит-нота',
                    'customer'         => 'Клиент',
                    'invoice-date'     => 'Дата счёта',
                    'due-date'         => 'Срок оплаты',
                    'payment-term'     => 'Условие оплаты',
                ],
            ],
        ],

        'tabs' => [
            'invoice-lines' => [
                'title' => 'Строки счёта',

                'repeater' => [
                    'products' => [
                        'entries' => [
                            'product'             => 'Товар',
                            'quantity'            => 'Количество',
                            'unit'                => 'Единица измерения',
                            'taxes'               => 'Налоги',
                            'discount-percentage' => 'Скидка',
                            'unit-price'          => 'Цена за единицу',
                            'sub-total'           => 'Промежуточный итог',
                            'total'               => 'Итого',
                        ],
                    ],
                ],
            ],

            'other-information' => [
                'title'    => 'Другое Сведения',
                'fieldset' => [
                    'invoice' => [
                        'title'   => 'Счёт',
                        'entries' => [
                            'customer-reference' => 'Ссылка клиента',
                            'sales-person'       => 'Продажи Person',
                            'payment-reference'  => 'Оплата Ссылка',
                            'recipient-bank'     => 'Получатель Банк',
                            'delivery-date'      => 'Доставка Дата',
                        ],
                    ],

                    'accounting' => [
                        'title' => 'Учёт',

                        'fieldset' => [
                            'incoterm'          => 'Incoterm',
                            'incoterm-location' => 'Incoterm Место',
                            'payment-method'    => 'Оплата Method',
                            'auto-post'         => 'Auto Post',
                            'checked'           => 'Checked',
                        ],
                    ],

                    'additional-information' => [
                        'title'   => 'Additional Сведения',
                        'entries' => [
                            'company'  => 'Компания',
                            'currency' => 'Валюта',
                        ],
                    ],

                    'marketing' => [
                        'title'   => 'Marketing',
                        'entries' => [
                            'campaign' => 'Campaign',
                            'medium'   => 'Средний',
                            'source'   => 'Источник',
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
