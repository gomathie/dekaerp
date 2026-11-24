<?php

return [
    'global-search' => [
        'vendor'    => 'Поставщик',
        'reference' => 'Номер',
        'amount'    => 'Сумма',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Основное',

                'fields' => [
                    'vendor'                   => 'Поставщик',
                    'vendor-reference'         => 'Номер у поставщика',
                    'vendor-reference-tooltip' => 'Номер заказа на продажу или предложения поставщика. Используется для сопоставления при приёмке товаров, так как обычно указывается в накладной поставщика.',
                    'agreement'                => 'Соглашение',
                    'currency'                 => 'Валюта',
                    'confirmation-date'        => 'Дата подтверждения',
                    'order-deadline'           => 'Крайний срок заказа',
                    'expected-arrival'         => 'Ожидаемое поступление',
                    'confirmed-by-vendor'      => 'Подтверждено поставщиком',
                    'deliver-to'               => 'Доставить в',
                ],
            ],
        ],

        'tabs' => [
            'products' => [
                'title' => 'Товары',

                'repeater' => [
                    'products' => [
                        'title'            => 'Товары',
                        'add-product-line' => 'Добавить товар',

                        'fields' => [
                            'product'             => 'Товар',
                            'expected-arrival'    => 'Ожидаемое поступление',
                            'quantity'            => 'Количество',
                            'received'            => 'Получено',
                            'billed'              => 'Выставлено',
                            'unit'                => 'Ед. изм.',
                            'packaging-qty'       => 'Кол-во упаковок',
                            'packaging'           => 'Упаковка',
                            'taxes'               => 'Налоги',
                            'discount-percentage' => 'Скидка (%)',
                            'unit-price'          => 'Цена за единицу',
                            'amount'              => 'Сумма',
                        ],

                        'notifications' => [
                            'quantity-below-received' => [
                                'title' => 'Нельзя уменьшить количество',
                                'body'  => 'Нельзя уменьшить количество ниже полученного (:qty).',
                            ],

                            'blanket-order-qty-limit' => [
                                'title' => 'Количество превышает лимит рамочного заказа',
                                'body'  => 'Количество товара (:product_qty) превышает доступное количество (:available_qty) по рамочному заказу.',
                            ],
                        ],

                        'columns' => [
                            'product'             => 'Товар',
                            'expected-arrival'    => 'Ожидаемое поступление',
                            'quantity'            => 'Количество',
                            'received'            => 'Получено',
                            'billed'              => 'Выставлено',
                            'unit'                => 'Ед. изм.',
                            'packaging-qty'       => 'Кол-во упаковок',
                            'packaging'           => 'Упаковка',
                            'taxes'               => 'Налоги',
                            'discount-percentage' => 'Скидка (%)',
                            'unit-price'          => 'Цена за единицу',
                            'amount'              => 'Сумма',
                        ],

                        'delete-action' => [
                            'error' => [
                                'title' => 'Нельзя удалить товар',
                                'body'  => 'Из подтверждённого заказа на закупку нельзя удалять товары.',
                            ],
                        ],

                        'actions' => [
                            'open-product' => [
                                'tooltip' => 'Открыть товар',
                            ],
                        ],
                    ],

                    'section' => [
                        'title' => 'Добавить раздел',

                        'fields' => [],
                    ],

                    'note' => [
                        'title' => 'Добавить примечание',

                        'fields' => [],
                    ],
                ],
            ],

            'additional' => [
                'title' => 'Дополнительная информация',

                'fields' => [
                    'buyer'             => 'Закупщик',
                    'company'           => 'Компания',
                    'source-document'   => 'Исходный документ',
                    'incoterm'          => 'Инкотермс',
                    'incoterm-tooltip'  => 'Инкотермс (Incoterms) — набор стандартных условий международной торговли, определяющих обязанности покупателя и продавца.',
                    'incoterm-location' => 'Место по Инкотермс',
                    'payment-term'      => 'Условие оплаты',
                    'fiscal-position'   => 'Налоговая позиция',
                ],
            ],

            'terms' => [
                'title' => 'Условия',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'favorite'         => 'Избранное',
            'priority'         => 'Приоритет',
            'vendor-reference' => 'Номер у поставщика',
            'reference'        => 'Номер',
            'vendor'           => 'Поставщик',
            'buyer'            => 'Закупщик',
            'company'          => 'Компания',
            'order-deadline'   => 'Крайний срок заказа',
            'source-document'  => 'Исходный документ',
            'untaxed-amount'   => 'Сумма без налогов',
            'total-amount'     => 'Общая сумма',
            'status'           => 'Статус',
            'billing-status'   => 'Статус выставления счетов',
            'receipt-status'   => 'Статус поступления',
            'currency'         => 'Валюта',
        ],

        'groups' => [
            'vendor'     => 'Поставщик',
            'buyer'      => 'Закупщик',
            'state'      => 'Статус',
            'created-at' => 'Создано',
            'updated-at' => 'Обновлено',
        ],

        'filters' => [
            'status'           => 'Статус',
            'vendor-reference' => 'Номер у поставщика',
            'reference'        => 'Номер',
            'untaxed-amount'   => 'Сумма без налогов',
            'total-amount'     => 'Общая сумма',
            'order-deadline'   => 'Крайний срок заказа',
            'vendor'           => 'Поставщик',
            'buyer'            => 'Закупщик',
            'company'          => 'Компания',
            'payment-term'     => 'Условие оплаты',
            'incoterm'         => 'Инкотермс',
            'status'           => 'Статус',
            'created-at'       => 'Создано',
            'updated-at'       => 'Обновлено',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Заказ удалён',
                        'body'  => 'Заказ успешно удалён.',
                    ],

                    'error' => [
                        'title' => 'Не удалось удалить заказ',
                        'body'  => 'Заказ нельзя удалить, так как он используется.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Заказы удалены',
                        'body'  => 'Заказы успешно удалены.',
                    ],

                    'error' => [
                        'title' => 'Не удалось удалить заказы',
                        'body'  => 'Заказы нельзя удалить, так как они используются.',
                    ],
                ],
            ],
        ],
    ],

    'summary' => [
        'tax' => 'Налог',
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Основное',

                'entries' => [
                    'purchase-order'           => 'Заказ на закупку',
                    'vendor'                   => 'Поставщик',
                    'vendor-reference'         => 'Номер у поставщика',
                    'vendor-reference-tooltip' => 'Номер заказа на продажу или предложения поставщика. Используется для сопоставления при приёмке товаров, так как обычно указывается в накладной поставщика.',
                    'agreement'                => 'Соглашение',
                    'currency'                 => 'Валюта',
                    'confirmation-date'        => 'Дата подтверждения',
                    'order-deadline'           => 'Крайний срок заказа',
                    'expected-arrival'         => 'Ожидаемое поступление',
                    'confirmed-by-vendor'      => 'Подтверждено поставщиком',
                ],
            ],
        ],

        'tabs' => [
            'products' => [
                'title' => 'Товары',

                'repeater' => [
                    'products' => [
                        'title'            => 'Товары',
                        'add-product-line' => 'Добавить товар',

                        'entries' => [
                            'product'             => 'Товар',
                            'expected-arrival'    => 'Ожидаемое поступление',
                            'quantity'            => 'Количество',
                            'received'            => 'Получено',
                            'billed'              => 'Выставлено',
                            'unit'                => 'Ед. изм.',
                            'packaging-qty'       => 'Кол-во упаковок',
                            'packaging'           => 'Упаковка',
                            'taxes'               => 'Налоги',
                            'discount-percentage' => 'Скидка (%)',
                            'unit-price'          => 'Цена за единицу',
                            'amount'              => 'Сумма',
                        ],
                    ],

                    'section' => [
                        'title' => 'Добавить раздел',
                    ],

                    'note' => [
                        'title' => 'Добавить примечание',
                    ],
                ],
            ],

            'additional' => [
                'title' => 'Дополнительная информация',

                'entries' => [
                    'buyer'             => 'Закупщик',
                    'company'           => 'Компания',
                    'source-document'   => 'Исходный документ',
                    'incoterm'          => 'Инкотермс',
                    'incoterm-tooltip'  => 'Инкотермс (Incoterms) — набор стандартных условий международной торговли, определяющих обязанности покупателя и продавца.',
                    'incoterm-location' => 'Место по Инкотермс',
                    'payment-term'      => 'Условие оплаты',
                    'fiscal-position'   => 'Налоговая позиция',
                ],
            ],

            'terms' => [
                'title' => 'Условия',
            ],
        ],
    ],
];
