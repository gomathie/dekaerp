<?php

return [
    'title' => 'Кредит-нота',

    'navigation' => [
        'title' => 'Кредит-ноты',
        'group' => 'Счета',
    ],

    'form' => [
        'section' => [
            'general' => [
                'title'  => 'Общее',
                'fields' => [
                    'customer-invoice' => 'Кредит-нота клиента',
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
                    'invoice' => [
                        'title'  => 'Счёт',
                        'fields' => [
                            'customer-reference' => 'Ссылка клиента',
                            'sales-person'       => 'Менеджер по продажам',
                            'payment-reference'  => 'Назначение платежа',
                            'recipient-bank'     => 'Банк получателя',
                            'delivery-date'      => 'Дата отгрузки',
                        ],
                    ],

                    'accounting' => [
                        'title' => 'Учёт',

                        'fields' => [
                            'incoterm'          => 'Инкотермс',
                            'incoterm-location' => 'Место по Инкотермс',
                            'payment-method'    => 'Способ оплаты',
                            'auto-post'         => 'Автопроведение',
                            'checked'           => 'Проверен',
                        ],
                    ],

                    'additional-information' => [
                        'title'  => 'Дополнительная информация',
                        'fields' => [
                            'company'  => 'Компания',
                            'currency' => 'Валюта',
                        ],
                    ],

                    'marketing' => [
                        'title'  => 'Маркетинг',
                        'fields' => [
                            'campaign' => 'Кампания',
                            'medium'   => 'Канал',
                            'source'   => 'Источник',
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
                    'customer-invoice' => 'Кредит-нота клиента',
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
                            'discount-percentage' => 'Скидка, %',
                            'unit-price'          => 'Цена за ед.',
                            'sub-total'           => 'Подытог',
                            'total'               => 'Итого',
                        ],
                    ],
                ],
            ],

            'other-information' => [
                'title'    => 'Прочая информация',
                'fieldset' => [
                    'invoice' => [
                        'title'   => 'Счёт',
                        'entries' => [
                            'customer-reference' => 'Ссылка клиента',
                            'sales-person'       => 'Менеджер по продажам',
                            'payment-reference'  => 'Назначение платежа',
                            'recipient-bank'     => 'Банк получателя',
                            'delivery-date'      => 'Дата отгрузки',
                        ],
                    ],

                    'accounting' => [
                        'title' => 'Учёт',

                        'fieldset' => [
                            'incoterm'          => 'Инкотермс',
                            'incoterm-location' => 'Место по Инкотермс',
                            'payment-method'    => 'Способ оплаты',
                            'auto-post'         => 'Автопроведение',
                            'checked'           => 'Проверен',
                        ],
                    ],

                    'additional-information' => [
                        'title'   => 'Дополнительная информация',
                        'entries' => [
                            'company'  => 'Компания',
                            'currency' => 'Валюта',
                        ],
                    ],

                    'marketing' => [
                        'title'   => 'Маркетинг',
                        'entries' => [
                            'campaign' => 'Кампания',
                            'medium'   => 'Канал',
                            'source'   => 'Источник',
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
