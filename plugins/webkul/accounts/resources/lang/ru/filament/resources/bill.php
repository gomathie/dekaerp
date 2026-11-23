<?php

return [
    'title' => 'Счёт',

    'navigation' => [
        'title' => 'Счета',
        'group' => 'Счета',
    ],

    'global-search' => [
        'vendor'   => 'Поставщик',
        'date'     => 'Дата',
        'due-date' => 'Срок оплаты',
    ],

    'form' => [
        'section' => [
            'general' => [
                'title'  => 'Общее',
                'fields' => [
                    'vendor-bill'       => 'Счёт поставщика',
                    'vendor'            => 'Поставщик',
                    'bill-date'         => 'Счёт поставщика Дата',
                    'bill-reference'    => 'Счёт поставщика Ссылка',
                    'accounting-date'   => 'Учёт Дата',
                    'payment-reference' => 'Оплата Ссылка',
                    'recipient-bank'    => 'Получатель Банк',
                    'due-date'          => 'Срок оплаты',
                    'payment-term'      => 'Условие оплаты',
                    'journal'           => 'Журнал',
                    'currency'          => 'Валюта',
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

                        'columns' => [
                            'product'             => 'Товар',
                            'quantity'            => 'Количество',
                            'unit'                => 'Единица',
                            'taxes'               => 'Налоги',
                            'discount-percentage' => 'Скидка',
                            'unit-price'          => 'Цена за единицу',
                            'sub-total'           => 'Промежуточный итог',
                        ],

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
                            'company'                 => 'Компания',
                            'incoterm'                => 'Incoterm',
                            'incoterm-location'       => 'Incoterm Место',
                            'payment-method'          => 'Оплата Method',
                            'fiscal-position'         => 'Финансовый Должность',
                            'fiscal-position-tooltip' => 'Финансовый positions используется до adapt налоги и счета based вкл. клиент место.',
                            'cash-rounding'           => 'Касса Rounding Method',
                            'cash-rounding-tooltip'   => 'Specifies smallest касса-к оплате единица валюта.',
                            'auto-post'               => 'Auto Post',
                            'checked'                 => 'Checked',
                        ],
                    ],
                ],
            ],

            'term-and-conditions' => [
                'title' => 'Term & Conditions',
            ],
        ],
    ],

    'table' => [
        'total'   => 'Итого',
        'columns' => [
            'number'           => 'Номер',
            'state'            => 'Состояние',
            'customer'         => 'Клиент',
            'bill-date'        => 'Счёт поставщика Дата',
            'checked'          => 'Checked',
            'accounting-date'  => 'Учёт',
            'due-date'         => 'Срок оплаты',
            'source-document'  => 'Источник Документ',
            'reference'        => 'Ссылка',
            'sales-person'     => 'Продажи Person',
            'tax-excluded'     => 'Налог Исключено',
            'tax'              => 'Налог',
            'total'            => 'Итого',
            'amount-due'       => 'Сумма Due',
            'bill-currency'    => 'Счёт поставщика Валюта',
        ],

        'summarizers' => [
            'total' => 'Итого',
        ],

        'groups' => [
            'name'                         => 'Название',
            'bill-partner-display-name'    => 'Счёт поставщика Контрагент Display Название',
            'bill-date'                    => 'Счёт поставщика Дата',
            'checked'                      => 'Checked',
            'date'                         => 'Дата',
            'bill-due-date'                => 'Счёт поставщика Срок оплаты',
            'bill-origin'                  => 'Счёт поставщика Происхождение',
            'sales-person'                 => 'Продажи Person',
            'currency'                     => 'Валюта',
            'created-at'                   => 'Создано',
            'updated-at'                   => 'Обновлено',
        ],

        'filters' => [
            'number'                    => 'Номер',
            'bill-partner-display-name' => 'Счёт поставщика Контрагент Display Название',
            'bill-date'                 => 'Счёт поставщика Дата',
            'bill-due-date'             => 'Счёт поставщика Срок оплаты',
            'bill-origin'               => 'Счёт поставщика Происхождение',
            'reference'                 => 'Ссылка',
            'payment-reference'         => 'Оплата Ссылка',
            'narration'                 => 'Narration',
            'partner'                   => 'Контрагент',
            'journal'                   => 'Журнал',
            'fiscal-position'           => 'Финансовый Должность',
            'currency'                  => 'Валюта',
            'company'                   => 'Компания',
            'date'                      => 'Учёт Дата',
            'delivery-date'             => 'Доставка Дата',
            'amount-untaxed'            => 'Без налога Сумма',
            'amount-tax'                => 'Налог Сумма',
            'amount-total'              => 'Итого Сумма',
            'amount-residual'           => 'Сумма Due',
            'checked'                   => 'Checked',
            'posted-before'             => 'Проведён До',
            'is-move-sent'              => 'Отправлено',
            'created-at'                => 'Создано',
            'updated-at'                => 'Обновлено',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Оплата удалено',
                    'body'  => 'оплата есть удалено successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Оплаты удалено',
                    'body'  => 'оплаты есть удалено successfully.',
                ],
            ],
        ],

        'toolbar-actions' => [
            'export' => [
                'label' => 'Экспорт',
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
                    'journal'           => 'Журнал',
                    'currency'          => 'Валюта',
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
                            'company'           => 'Компания',
                            'incoterm'          => 'Incoterm',
                            'incoterm-location' => 'Incoterm Место',
                            'payment-method'    => 'Оплата Method',
                            'checked'           => 'Checked',
                            'fiscal-position'   => 'Финансовый Должность',
                            'cash-rounding'     => 'Касса Rounding Method',
                            'checked'           => 'Checked',
                        ],
                    ],
                ],
            ],

            'term-and-conditions' => [
                'title' => 'Term & Conditions',
            ],

            'journal-items' => [
                'title' => 'Журнал Позиции',

                'repeater' => [
                    'entries' => [
                        'account'  => 'Счёт',
                        'partner'  => 'Контрагент',
                        'label'    => 'Подпись',
                        'due-date' => 'Срок оплаты',
                        'currency' => 'Валюта',
                        'taxes'    => 'Налоги',
                        'debit'    => 'Дебет',
                        'credit'   => 'Кредит',
                    ],
                ],
            ],
        ],
    ],
];
