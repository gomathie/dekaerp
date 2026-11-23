<?php

return [
    'title' => 'Коммерческое предложение',

    'navigation' => [
        'title' => 'Коммерческие предложения',
    ],

    'global-search' => [
        'customer'  => 'Клиент',
        'reference' => 'Ссылка',
        'amount'    => 'Сумма',
    ],

    'form' => [
        'section' => [
            'general' => [
                'title'  => 'Общее',
                'fields' => [
                    'customer'       => 'Клиент',
                    'expiration'     => 'Expiration',
                    'quotation-date' => 'Коммерческое предложение Дата',
                    'order-date'     => 'Заказ Дата',
                    'payment-term'   => 'Условие оплаты',
                ],
            ],
        ],

        'tabs' => [
            'order-line' => [
                'title' => 'Заказ Строка',

                'repeater' => [
                    'products' => [
                        'title'       => 'Товары',
                        'add-product' => 'Добавить товар',

                        'columns'     => [
                            'product'                    => 'Товар',
                            'product-variants'           => 'Товар Variants',
                            'product-simple'             => 'Товар Simple',
                            'quantity'                   => 'Количество',
                            'insufficient-stock-tooltip' => 'Insufficient запас до fulfill этот потребность.',
                            'uom'                        => 'UOM',
                            'lead-time'                  => 'Lead Time',
                            'qty-delivered'              => 'Delivered',
                            'qty-invoiced'               => 'Invoiced',
                            'packaging-qty'              => 'Упаковка Количество',
                            'packaging'                  => 'Упаковка',
                            'unit-price'                 => 'Цена за единицу',
                            'cost'                       => 'Стоимость',
                            'margin'                     => 'Маржа',
                            'taxes'                      => 'Налоги',
                            'amount'                     => 'Сумма',
                            'margin-percentage'          => 'Маржа (%)',
                            'discount-percentage'        => 'Скидка (%)',
                        ],

                        'fields'      => [
                            'product'             => 'Товар',
                            'product-variants'    => 'Товар Variants',
                            'product-simple'      => 'Товар Simple',
                            'quantity'            => 'Количество',
                            'uom'                 => 'Единица измерения',
                            'lead-time'           => 'Lead Time',
                            'qty-delivered'       => 'Количество Delivered',
                            'qty-invoiced'        => 'Количество Invoiced',
                            'packaging-qty'       => 'Упаковка Количество',
                            'packaging'           => 'Упаковка',
                            'unit-price'          => 'Цена за единицу',
                            'cost'                => 'Стоимость',
                            'margin'              => 'Маржа',
                            'taxes'               => 'Налоги',
                            'amount'              => 'Сумма',
                            'margin-percentage'   => 'Маржа (%)',
                            'discount-percentage' => 'Скидка (%)',
                        ],

                        'notifications' => [
                            'quantity-below-delivered' => [
                                'title' => 'Нельзя Reduce Количество',
                                'body'  => 'Вы нельзя reduce количество ниже delivered количество (:qty).',
                            ],
                        ],

                        'delete-action' => [
                            'error' => [
                                'title' => 'Нельзя Удалить Товар',
                                'body'  => 'Товары нельзя быть удалено от подтверждён заказ на продажу.',
                            ],
                        ],

                        'actions' => [
                            'open-product' => [
                                'tooltip' => 'Открыт товар',
                            ],
                        ],
                    ],

                    'product-optional' => [
                        'title'       => 'Необязательно Товары',
                        'add-product' => 'Добавить товар',

                        'columns' => [
                            'product'             => 'Товар',
                            'description'         => 'Описание',
                            'quantity'            => 'Количество',
                            'uom'                 => 'Единица измерения',
                            'unit-price'          => 'Цена за единицу',
                            'discount-percentage' => 'Скидка (%)',
                        ],

                        'fields'      => [
                            'product'             => 'Товар',
                            'description'         => 'Описание',
                            'quantity'            => 'Количество',
                            'uom'                 => 'Единица измерения',
                            'unit-price'          => 'Цена за единицу',
                            'discount-percentage' => 'Скидка (%)',

                            'actions' => [
                                'tooltip' => [
                                    'add-order-line' => 'Добавить Заказ Строка',
                                    'already-added'  => 'Уже Added До Заказ',
                                ],

                                'notifications' => [
                                    'product-added' => [
                                        'title' => 'Товар added',
                                        'body'  => 'товар есть added successfully.',
                                    ],

                                    'product-not-found' => [
                                        'title' => 'Товар Не Найдено',
                                    ],

                                    'product-already-exists' => [
                                        'title' => 'Товар Уже Существует',
                                        'body'  => 'Этот товар уже в заказ строки. Пожалуйста обновить existing строка instead.',
                                    ],

                                    'missing-product-data' => [
                                        'title' => 'Отсутствует Товар Data',
                                        'body'  => 'Не удалось до process выбрано товар.',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'other-information' => [
                'title' => 'Другое Сведения',

                'fieldset' => [
                    'sales' => [
                        'title' => 'Продажи',

                        'fields' => [
                            'sales-person'       => 'Продажи Person',
                            'customer-reference' => 'Ссылка клиента',
                            'tags'               => 'Метки',
                        ],
                    ],

                    'shipping' => [
                        'title'  => 'Shipping',
                        'fields' => [
                            'warehouse'       => 'Склад',
                            'commitment-date' => 'Доставка Дата',
                        ],
                    ],

                    'tracking' => [
                        'title'  => 'Tracking',
                        'fields' => [
                            'source-document' => 'Источник Документ',
                            'medium'          => 'Средний',
                            'source'          => 'Источник',
                            'campaign'        => 'Campaign',
                        ],
                    ],

                    'additional-information' => [
                        'title' => 'Additional Сведения',

                        'fields' => [
                            'company'  => 'Компания',
                            'currency' => 'Валюта',
                        ],
                    ],
                ],
            ],

            'term-and-conditions' => [
                'title' => 'Terms & Conditions',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'number'             => 'Номер',
            'status'             => 'Статус',
            'delivery-status'    => 'Доставка Статус',
            'invoice-status'     => 'Счёт Статус',
            'order-date'         => 'Заказ Дата',
            'creation-date'      => 'Creation Дата',
            'commitment-date'    => 'Commitment Дата',
            'expected-date'      => 'Ожидается Дата',
            'customer'           => 'Клиент',
            'sales-person'       => 'Продажи Person',
            'sales-team'         => 'Продажи Команда',
            'untaxed-amount'     => 'Без налога Сумма',
            'amount-tax'         => 'Сумма Налог',
            'amount-total'       => 'Сумма Итого',
            'customer-reference' => 'Ссылка клиента',
        ],

        'summarizers' => [
            'total'        => 'Итого',
            'taxes'        => 'Налоги',
            'total-amount' => 'Итого Сумма',
        ],

        'filters' => [
            'sales-person'     => 'Продажи Person',
            'utm-source'       => 'UTM Источник',
            'company'          => 'Компания',
            'customer'         => 'Клиент',
            'journal'          => 'Журнал',
            'invoice-address'  => 'Счёт Адрес',
            'shipping-address' => 'Shipping Адрес',
            'fiscal-position'  => 'Финансовый Должность',
            'payment-term'     => 'Условие оплаты',
            'currency'         => 'Валюта',
            'created-at'       => 'Создано',
            'updated-at'       => 'Обновлено',
        ],

        'groups' => [
            'medium'          => 'Средний',
            'source'          => 'Источник',
            'team'            => 'Команда',
            'sales-person'    => 'Продажи Person',
            'currency'        => 'Валюта',
            'company'         => 'Компания',
            'customer'        => 'Клиент',
            'quotation-date'  => 'Коммерческое предложение Дата',
            'commitment-date' => 'Commitment Дата',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Коммерческое предложение restored',
                    'body'  => 'коммерческое предложение есть restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Коммерческое предложение удалено',
                    'body'  => 'коммерческое предложение есть удалено successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Коммерческое предложение force удалено',
                    'body'  => 'коммерческое предложение есть force удалено successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Коммерческие предложения restored',
                    'body'  => 'коммерческие предложения есть restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Коммерческие предложения удалено',
                    'body'  => 'коммерческие предложения есть удалено successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Коммерческие предложения force удалено',
                    'body'  => 'коммерческие предложения есть force удалено successfully.',
                ],
            ],
        ],

        'empty-state-action' => [
            'create' => [
                'notification' => [
                    'title' => 'Коммерческие предложения создано',
                    'body'  => 'коммерческие предложения есть создано successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'section' => [
            'general' => [
                'title'   => 'Общее',
                'entries' => [
                    'sale-order'     => 'Заказ на продажу',
                    'customer'       => 'Клиент',
                    'expiration'     => 'Expiration',
                    'quotation-date' => 'Коммерческое предложение Дата',
                    'payment-term'   => 'Условие оплаты',
                ],
            ],
        ],

        'tabs' => [
            'order-line' => [
                'title' => 'Заказ Строка',

                'repeater' => [
                    'products' => [
                        'title'       => 'Товары',
                        'add-product' => 'Добавить товар',
                        'entries'     => [
                            'product'             => 'Товар',
                            'product-variants'    => 'Товар Variants',
                            'product-simple'      => 'Товар Simple',
                            'quantity'            => 'Количество',
                            'qty-delivered'       => 'Delivered',
                            'qty-invoiced'        => 'Invoiced',
                            'uom'                 => 'UOM',
                            'lead-time'           => 'Lead Time',
                            'packaging-qty'       => 'Упаковка Количество',
                            'packaging'           => 'Упаковка',
                            'unit-price'          => 'Цена за единицу',
                            'cost'                => 'Стоимость',
                            'margin'              => 'Маржа',
                            'taxes'               => 'Налоги',
                            'amount'              => 'Сумма',
                            'margin-percentage'   => 'Маржа (%)',
                            'discount-percentage' => 'Скидка (%)',
                            'sub-total'           => 'Промежуточный итог',
                        ],
                    ],

                    'product-optional' => [
                        'title'       => 'Необязательно Товары',
                        'add-product' => 'Добавить товар',
                        'entries'     => [
                            'product'             => 'Товар',
                            'description'         => 'Описание',
                            'quantity'            => 'Количество',
                            'uom'                 => 'Единица измерения',
                            'unit-price'          => 'Цена за единицу',
                            'discount-percentage' => 'Скидка (%)',
                            'sub-total'           => 'Промежуточный итог',

                            'actions' => [
                                'tooltip' => [
                                    'add-order-line' => 'Добавить Заказ Строка',
                                ],

                                'notifications' => [
                                    'product-added' => [
                                        'title' => 'Товар added',
                                        'body'  => 'товар есть added successfully.',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'other-information' => [
                'title' => 'Другое Сведения',

                'fieldset' => [
                    'sales' => [
                        'title' => 'Продажи',

                        'entries' => [
                            'sales-person'       => 'Продажи Person',
                            'customer-reference' => 'Ссылка клиента',
                            'tags'               => 'Метки',
                        ],
                    ],

                    'shipping' => [
                        'title'   => 'Shipping',
                        'entries' => [
                            'commitment-date' => 'Доставка Дата',
                        ],
                    ],

                    'tracking' => [
                        'title'   => 'Tracking',
                        'entries' => [
                            'source-document' => 'Источник Документ',
                            'medium'          => 'Средний',
                            'source'          => 'Источник',
                            'campaign'        => 'Campaign',
                        ],
                    ],

                    'additional-information' => [
                        'title' => 'Additional Сведения',

                        'entries' => [
                            'company'  => 'Компания',
                            'currency' => 'Валюта',
                        ],
                    ],
                ],
            ],

            'term-and-conditions' => [
                'title' => 'Terms & Conditions',
            ],
        ],
    ],
];
