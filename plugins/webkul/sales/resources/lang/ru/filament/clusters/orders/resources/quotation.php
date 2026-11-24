<?php

return [
    'title' => 'Коммерческое предложение',

    'navigation' => [
        'title' => 'Коммерческие предложения',
    ],

    'global-search' => [
        'customer'  => 'Клиент',
        'reference' => 'Референс',
        'amount'    => 'Сумма',
    ],

    'form' => [
        'section' => [
            'general' => [
                'title'  => 'Основное',
                'fields' => [
                    'customer'       => 'Клиент',
                    'expiration'     => 'Действует до',
                    'quotation-date' => 'Дата предложения',
                    'order-date'     => 'Дата заказа',
                    'payment-term'   => 'Условия оплаты',
                ],
            ],
        ],

        'tabs' => [
            'order-line' => [
                'title' => 'Строки заказа',

                'repeater' => [
                    'products' => [
                        'title'       => 'Товары',
                        'add-product' => 'Добавить товар',

                        'columns'     => [
                            'product'                    => 'Товар',
                            'product-variants'           => 'Варианты товара',
                            'product-simple'             => 'Простой товар',
                            'quantity'                   => 'Количество',
                            'insufficient-stock-tooltip' => 'Недостаточно запасов для выполнения этого спроса.',
                            'uom'                        => 'Ед. изм.',
                            'lead-time'                  => 'Срок поставки',
                            'qty-delivered'              => 'Отгружено',
                            'qty-invoiced'               => 'Выставлено',
                            'packaging-qty'              => 'Кол-во упаковок',
                            'packaging'                  => 'Упаковка',
                            'unit-price'                 => 'Цена за ед.',
                            'cost'                       => 'Себестоимость',
                            'margin'                     => 'Маржа',
                            'taxes'                      => 'Налоги',
                            'amount'                     => 'Сумма',
                            'margin-percentage'          => 'Маржа (%)',
                            'discount-percentage'        => 'Скидка (%)',
                        ],

                        'fields'      => [
                            'product'             => 'Товар',
                            'product-variants'    => 'Варианты товара',
                            'product-simple'      => 'Простой товар',
                            'quantity'            => 'Количество',
                            'uom'                 => 'Единица измерения',
                            'lead-time'           => 'Срок поставки',
                            'qty-delivered'       => 'Отгружено',
                            'qty-invoiced'        => 'Выставлено',
                            'packaging-qty'       => 'Кол-во упаковок',
                            'packaging'           => 'Упаковка',
                            'unit-price'          => 'Цена за ед.',
                            'cost'                => 'Себестоимость',
                            'margin'              => 'Маржа',
                            'taxes'               => 'Налоги',
                            'amount'              => 'Сумма',
                            'margin-percentage'   => 'Маржа (%)',
                            'discount-percentage' => 'Скидка (%)',
                        ],

                        'notifications' => [
                            'quantity-below-delivered' => [
                                'title' => 'Нельзя уменьшить количество',
                                'body'  => 'Нельзя уменьшить количество ниже отгруженного (:qty).',
                            ],
                        ],

                        'delete-action' => [
                            'error' => [
                                'title' => 'Нельзя удалить товар',
                                'body'  => 'Из подтверждённого заказа на продажу нельзя удалять товары.',
                            ],
                        ],

                        'actions' => [
                            'open-product' => [
                                'tooltip' => 'Открыть товар',
                            ],
                        ],
                    ],

                    'product-optional' => [
                        'title'       => 'Дополнительные товары',
                        'add-product' => 'Добавить товар',

                        'columns' => [
                            'product'             => 'Товар',
                            'description'         => 'Описание',
                            'quantity'            => 'Количество',
                            'uom'                 => 'Единица измерения',
                            'unit-price'          => 'Цена за ед.',
                            'discount-percentage' => 'Скидка (%)',
                        ],

                        'fields'      => [
                            'product'             => 'Товар',
                            'description'         => 'Описание',
                            'quantity'            => 'Количество',
                            'uom'                 => 'Единица измерения',
                            'unit-price'          => 'Цена за ед.',
                            'discount-percentage' => 'Скидка (%)',

                            'actions' => [
                                'tooltip' => [
                                    'add-order-line' => 'Добавить строку заказа',
                                    'already-added'  => 'Уже в заказе',
                                ],

                                'notifications' => [
                                    'product-added' => [
                                        'title' => 'Товар добавлен',
                                        'body'  => 'Товар успешно добавлен.',
                                    ],

                                    'product-not-found' => [
                                        'title' => 'Товар не найден',
                                    ],

                                    'product-already-exists' => [
                                        'title' => 'Товар уже добавлен',
                                        'body'  => 'Этот товар уже есть в строках заказа. Измените существующую строку.',
                                    ],

                                    'missing-product-data' => [
                                        'title' => 'Нет данных о товаре',
                                        'body'  => 'Не удалось обработать выбранный товар.',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'other-information' => [
                'title' => 'Прочие сведения',

                'fieldset' => [
                    'sales' => [
                        'title' => 'Продажи',

                        'fields' => [
                            'sales-person'       => 'Менеджер по продажам',
                            'customer-reference' => 'Референс клиента',
                            'tags'               => 'Теги',
                        ],
                    ],

                    'shipping' => [
                        'title'  => 'Доставка',
                        'fields' => [
                            'warehouse'       => 'Склад',
                            'commitment-date' => 'Дата отгрузки',
                        ],
                    ],

                    'tracking' => [
                        'title'  => 'Отслеживание',
                        'fields' => [
                            'source-document' => 'Исходный документ',
                            'medium'          => 'Канал',
                            'source'          => 'Источник',
                            'campaign'        => 'Кампания',
                        ],
                    ],

                    'additional-information' => [
                        'title' => 'Дополнительные сведения',

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

    'table' => [
        'columns' => [
            'number'             => 'Номер',
            'status'             => 'Статус',
            'delivery-status'    => 'Статус отгрузки',
            'invoice-status'     => 'Статус выставления',
            'order-date'         => 'Дата заказа',
            'creation-date'      => 'Дата создания',
            'commitment-date'    => 'Обещанная дата',
            'expected-date'      => 'Ожидаемая дата',
            'customer'           => 'Клиент',
            'sales-person'       => 'Менеджер по продажам',
            'sales-team'         => 'Команда продаж',
            'untaxed-amount'     => 'Сумма без налога',
            'amount-tax'         => 'Сумма налога',
            'amount-total'       => 'Итоговая сумма',
            'customer-reference' => 'Референс клиента',
        ],

        'summarizers' => [
            'total'        => 'Итого',
            'taxes'        => 'Налоги',
            'total-amount' => 'Итоговая сумма',
        ],

        'filters' => [
            'sales-person'     => 'Менеджер по продажам',
            'utm-source'       => 'Источник UTM',
            'company'          => 'Компания',
            'customer'         => 'Клиент',
            'journal'          => 'Журнал',
            'invoice-address'  => 'Адрес для счетов',
            'shipping-address' => 'Адрес доставки',
            'fiscal-position'  => 'Налоговая позиция',
            'payment-term'     => 'Условия оплаты',
            'currency'         => 'Валюта',
            'created-at'       => 'Создано',
            'updated-at'       => 'Обновлено',
        ],

        'groups' => [
            'medium'          => 'Канал',
            'source'          => 'Источник',
            'team'            => 'Команда',
            'sales-person'    => 'Менеджер по продажам',
            'currency'        => 'Валюта',
            'company'         => 'Компания',
            'customer'        => 'Клиент',
            'quotation-date'  => 'Дата предложения',
            'commitment-date' => 'Обещанная дата',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Предложение восстановлено',
                    'body'  => 'Предложение успешно восстановлено.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Предложение удалено',
                    'body'  => 'Предложение успешно удалено.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Предложение удалено навсегда',
                    'body'  => 'Предложение успешно удалено навсегда.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Предложения восстановлены',
                    'body'  => 'Предложения успешно восстановлены.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Предложения удалены',
                    'body'  => 'Предложения успешно удалены.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Предложения удалены навсегда',
                    'body'  => 'Предложения успешно удалены навсегда.',
                ],
            ],
        ],

        'empty-state-action' => [
            'create' => [
                'notification' => [
                    'title' => 'Предложения созданы',
                    'body'  => 'Предложения успешно созданы.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'section' => [
            'general' => [
                'title'   => 'Основное',
                'entries' => [
                    'sale-order'     => 'Заказ на продажу',
                    'customer'       => 'Клиент',
                    'expiration'     => 'Действует до',
                    'quotation-date' => 'Дата предложения',
                    'payment-term'   => 'Условия оплаты',
                ],
            ],
        ],

        'tabs' => [
            'order-line' => [
                'title' => 'Строки заказа',

                'repeater' => [
                    'products' => [
                        'title'       => 'Товары',
                        'add-product' => 'Добавить товар',
                        'entries'     => [
                            'product'             => 'Товар',
                            'product-variants'    => 'Варианты товара',
                            'product-simple'      => 'Простой товар',
                            'quantity'            => 'Количество',
                            'qty-delivered'       => 'Отгружено',
                            'qty-invoiced'        => 'Выставлено',
                            'uom'                 => 'Ед. изм.',
                            'lead-time'           => 'Срок поставки',
                            'packaging-qty'       => 'Кол-во упаковок',
                            'packaging'           => 'Упаковка',
                            'unit-price'          => 'Цена за ед.',
                            'cost'                => 'Себестоимость',
                            'margin'              => 'Маржа',
                            'taxes'               => 'Налоги',
                            'amount'              => 'Сумма',
                            'margin-percentage'   => 'Маржа (%)',
                            'discount-percentage' => 'Скидка (%)',
                            'sub-total'           => 'Подытог',
                        ],
                    ],

                    'product-optional' => [
                        'title'       => 'Дополнительные товары',
                        'add-product' => 'Добавить товар',
                        'entries'     => [
                            'product'             => 'Товар',
                            'description'         => 'Описание',
                            'quantity'            => 'Количество',
                            'uom'                 => 'Единица измерения',
                            'unit-price'          => 'Цена за ед.',
                            'discount-percentage' => 'Скидка (%)',
                            'sub-total'           => 'Подытог',

                            'actions' => [
                                'tooltip' => [
                                    'add-order-line' => 'Добавить строку заказа',
                                ],

                                'notifications' => [
                                    'product-added' => [
                                        'title' => 'Товар добавлен',
                                        'body'  => 'Товар успешно добавлен.',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'other-information' => [
                'title' => 'Прочие сведения',

                'fieldset' => [
                    'sales' => [
                        'title' => 'Продажи',

                        'entries' => [
                            'sales-person'       => 'Менеджер по продажам',
                            'customer-reference' => 'Референс клиента',
                            'tags'               => 'Теги',
                        ],
                    ],

                    'shipping' => [
                        'title'   => 'Доставка',
                        'entries' => [
                            'commitment-date' => 'Дата отгрузки',
                        ],
                    ],

                    'tracking' => [
                        'title'   => 'Отслеживание',
                        'entries' => [
                            'source-document' => 'Исходный документ',
                            'medium'          => 'Канал',
                            'source'          => 'Источник',
                            'campaign'        => 'Кампания',
                        ],
                    ],

                    'additional-information' => [
                        'title' => 'Дополнительные сведения',

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
