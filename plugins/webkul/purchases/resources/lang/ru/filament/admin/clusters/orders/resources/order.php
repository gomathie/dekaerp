<?php

return [
    'global-search' => [
        'vendor'    => 'Поставщик',
        'reference' => 'Ссылка',
        'amount'    => 'Сумма',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Общее',

                'fields' => [
                    'vendor'                   => 'Поставщик',
                    'vendor-reference'         => 'Поставщик Ссылка',
                    'vendor-reference-tooltip' => 'ссылка номер заказ на продажу или bid provided от поставщик. Это используется для matching когда receiving товары, как этот ссылка typically включено в vendor\'s доставка заказ.',
                    'agreement'                => 'Agreement',
                    'currency'                 => 'Валюта',
                    'confirmation-date'        => 'Confirmation Дата',
                    'order-deadline'           => 'Заказ Deadline',
                    'expected-arrival'         => 'Ожидается Arrival',
                    'confirmed-by-vendor'      => 'Подтверждён от Поставщик',
                    'deliver-to'               => 'Deliver До',
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
                            'expected-arrival'    => 'Ожидается Arrival',
                            'quantity'            => 'Количество',
                            'received'            => 'Получено',
                            'billed'              => 'Billed',
                            'unit'                => 'Единица',
                            'packaging-qty'       => 'Упаковка Qty',
                            'packaging'           => 'Упаковка',
                            'taxes'               => 'Налоги',
                            'discount-percentage' => 'Скидка (%)',
                            'unit-price'          => 'Цена за единицу',
                            'amount'              => 'Сумма',
                        ],

                        'notifications' => [
                            'quantity-below-received' => [
                                'title' => 'Нельзя Reduce Количество',
                                'body'  => 'Вы нельзя reduce количество ниже получено количество (:qty).',
                            ],

                            'blanket-order-qty-limit' => [
                                'title' => 'Количество Exceeds Blanket Заказ Limit',
                                'body'  => 'товар количество (:product_qty) exceeds доступно количество (:available_qty) от blanket заказ.',
                            ],
                        ],

                        'columns' => [
                            'product'             => 'Товар',
                            'expected-arrival'    => 'Ожидается Arrival',
                            'quantity'            => 'Количество',
                            'received'            => 'Получено',
                            'billed'              => 'Billed',
                            'unit'                => 'Единица',
                            'packaging-qty'       => 'Упаковка Qty',
                            'packaging'           => 'Упаковка',
                            'taxes'               => 'Налоги',
                            'discount-percentage' => 'Скидка (%)',
                            'unit-price'          => 'Цена за единицу',
                            'amount'              => 'Сумма',
                        ],

                        'delete-action' => [
                            'error' => [
                                'title' => 'Нельзя Удалить Товар',
                                'body'  => 'Товары нельзя быть удалено от подтверждён заказ на закупку.',
                            ],
                        ],

                        'actions' => [
                            'open-product' => [
                                'tooltip' => 'Открыт товар',
                            ],
                        ],
                    ],

                    'section' => [
                        'title' => 'Добавить Раздел',

                        'fields' => [],
                    ],

                    'note' => [
                        'title' => 'Добавить Заметка',

                        'fields' => [],
                    ],
                ],
            ],

            'additional' => [
                'title' => 'Additional Сведения',

                'fields' => [
                    'buyer'             => 'Buyer',
                    'company'           => 'Компания',
                    'source-document'   => 'Источник Документ',
                    'incoterm'          => 'Incoterm',
                    'incoterm-tooltip'  => 'International Commercial Terms (Incoterms) задать standardized trade terms используется в global transactions до define responsibilities между buyers и sellers.',
                    'incoterm-location' => 'Incoterm Место',
                    'payment-term'      => 'Условие оплаты',
                    'fiscal-position'   => 'Финансовый Должность',
                ],
            ],

            'terms' => [
                'title' => 'Terms и Conditions',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'favorite'         => 'Favorite',
            'priority'         => 'Приоритет',
            'vendor-reference' => 'Поставщик Ссылка',
            'reference'        => 'Ссылка',
            'vendor'           => 'Поставщик',
            'buyer'            => 'Buyer',
            'company'          => 'Компания',
            'order-deadline'   => 'Заказ Deadline',
            'source-document'  => 'Источник Документ',
            'untaxed-amount'   => 'Без налога Сумма',
            'total-amount'     => 'Итого Сумма',
            'status'           => 'Статус',
            'billing-status'   => 'Billing Статус',
            'receipt-status'   => 'Приход Статус',
            'currency'         => 'Валюта',
        ],

        'groups' => [
            'vendor'     => 'Поставщик',
            'buyer'      => 'Buyer',
            'state'      => 'Состояние',
            'created-at' => 'Создано',
            'updated-at' => 'Обновлено',
        ],

        'filters' => [
            'status'           => 'Статус',
            'vendor-reference' => 'Поставщик Ссылка',
            'reference'        => 'Ссылка',
            'untaxed-amount'   => 'Без налога Сумма',
            'total-amount'     => 'Итого Сумма',
            'order-deadline'   => 'Заказ Deadline',
            'vendor'           => 'Поставщик',
            'buyer'            => 'Buyer',
            'company'          => 'Компания',
            'payment-term'     => 'Условие оплаты',
            'incoterm'         => 'Incoterm',
            'status'           => 'Статус',
            'created-at'       => 'Создано',
            'updated-at'       => 'Обновлено',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Заказ удалено',
                        'body'  => 'заказ есть удалено successfully.',
                    ],

                    'error' => [
                        'title' => 'Заказ could не быть удалено',
                        'body'  => 'заказ нельзя быть удалено потому что это currently в использовать.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Заказы удалено',
                        'body'  => 'заказы есть удалено successfully.',
                    ],

                    'error' => [
                        'title' => 'Заказы could не быть удалено',
                        'body'  => 'заказы нельзя быть удалено потому что они currently в использовать.',
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
                'title' => 'Общее',

                'entries' => [
                    'purchase-order'           => 'Заказ на закупку',
                    'vendor'                   => 'Поставщик',
                    'vendor-reference'         => 'Поставщик Ссылка',
                    'vendor-reference-tooltip' => 'ссылка номер заказ на продажу или bid provided от поставщик. Это используется для matching когда receiving товары, как этот ссылка typically включено в vendor\'s доставка заказ.',
                    'agreement'                => 'Agreement',
                    'currency'                 => 'Валюта',
                    'confirmation-date'        => 'Confirmation Дата',
                    'order-deadline'           => 'Заказ Deadline',
                    'expected-arrival'         => 'Ожидается Arrival',
                    'confirmed-by-vendor'      => 'Подтверждён от Поставщик',
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
                            'expected-arrival'    => 'Ожидается Arrival',
                            'quantity'            => 'Количество',
                            'received'            => 'Получено',
                            'billed'              => 'Billed',
                            'unit'                => 'Единица',
                            'packaging-qty'       => 'Упаковка Qty',
                            'packaging'           => 'Упаковка',
                            'taxes'               => 'Налоги',
                            'discount-percentage' => 'Скидка (%)',
                            'unit-price'          => 'Цена за единицу',
                            'amount'              => 'Сумма',
                        ],
                    ],

                    'section' => [
                        'title' => 'Добавить Раздел',
                    ],

                    'note' => [
                        'title' => 'Добавить Заметка',
                    ],
                ],
            ],

            'additional' => [
                'title' => 'Additional Сведения',

                'entries' => [
                    'buyer'             => 'Buyer',
                    'company'           => 'Компания',
                    'source-document'   => 'Источник Документ',
                    'incoterm'          => 'Incoterm',
                    'incoterm-tooltip'  => 'International Commercial Terms (Incoterms) задать standardized trade terms используется в global transactions до define responsibilities между buyers и sellers.',
                    'incoterm-location' => 'Incoterm Место',
                    'payment-term'      => 'Условие оплаты',
                    'fiscal-position'   => 'Финансовый Должность',
                ],
            ],

            'terms' => [
                'title' => 'Terms и Conditions',
            ],
        ],
    ],
];
