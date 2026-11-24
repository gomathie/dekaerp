<?php

return [
    'title' => 'Счёт',

    'navigation' => [
        'title' => 'Счета',
        'group' => 'Счета',
    ],

    'global-search' => [
        'customer' => 'Клиент',
        'date'     => 'Дата',
        'due-date' => 'Срок оплаты',
    ],

    'form' => [
        'section' => [
            'general' => [
                'title'  => 'Общее',

                'fields' => [
                    'customer-invoice' => 'Счёт клиенту',
                    'customer'         => 'Клиент',
                    'invoice-date'     => 'Дата счёта',
                    'due-date'         => 'Срок оплаты',
                    'payment-term'     => 'Условие оплаты',
                    'journal'          => 'Журнал',
                    'currency'         => 'Валюта',
                ],
            ],
        ],

        'notifications' => [
            'missing-rate' => [
                'title' => 'Нет обменного курса',
                'body'  => 'Нет обменного курса между :from и :to, поэтому цены не пересчитаны. Добавьте курс в разделе Конфигурация → Валюты или введите цены вручную.',
            ],
        ],

        'tabs' => [
            'invoice-lines' => [
                'title' => 'Строки счёта',

                'repeater' => [
                    'products' => [
                        'title'       => 'Товары',
                        'add-product' => 'Добавить товар',
                        'price-hint'  => 'Цена, изменённая здесь, действует только для этого счёта. У товара остаётся своя цена.',

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
                            'company'                 => 'Компания',
                            'incoterm'                => 'Incoterm',
                            'incoterm-location'       => 'Incoterm Место',
                            'fiscal-position'         => 'Финансовый Должность',
                            'fiscal-position-tooltip' => 'Финансовый positions используется до adapt налоги и счета based вкл. клиент место.',
                            'cash-rounding'           => 'Касса Rounding Method',
                            'cash-rounding-tooltip'   => 'Specifies smallest касса-к оплате единица валюта.',
                            'payment-method'          => 'Оплата Method',
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
            'created-by'       => 'Создано От',
            'customer'         => 'Клиент',
            'invoice-date'     => 'Дата счёта',
            'checked'          => 'Checked',
            'accounting-date'  => 'Учёт',
            'due-date'         => 'Срок оплаты',
            'source-document'  => 'Источник Документ',
            'reference'        => 'Ссылка',
            'sales-person'     => 'Продажи Person',
            'tax-excluded'     => 'Налог Исключено',
            'tax'              => 'Налог',
            'total'            => 'Итого',
            'amount-paid'      => 'Оплачено',
            'amount-due'       => 'Сумма Due',
            'invoice-currency' => 'Счёт Валюта',
        ],

        'summarizers' => [
            'total' => 'Итого',
        ],

        'groups' => [
            'name'                         => 'Название',
            'invoice-partner-display-name' => 'Счёт Контрагент Display Название',
            'invoice-date'                 => 'Дата счёта',
            'checked'                      => 'Checked',
            'date'                         => 'Дата',
            'invoice-due-date'             => 'Счёт Срок оплаты',
            'invoice-origin'               => 'Счёт Происхождение',
            'sales-person'                 => 'Продажи Person',
            'currency'                     => 'Валюта',
            'created-at'                   => 'Создано',
            'updated-at'                   => 'Обновлено',
        ],

        'filters' => [
            'number'                       => 'Номер',
            'invoice-partner-display-name' => 'Счёт Контрагент Display Название',
            'invoice-date'                 => 'Дата счёта',
            'invoice-due-date'             => 'Счёт Срок оплаты',
            'invoice-origin'               => 'Счёт Происхождение',
            'reference'                    => 'Ссылка',
            'payment-reference'            => 'Оплата Ссылка',
            'narration'                    => 'Narration',
            'partner'                      => 'Контрагент',
            'journal'                      => 'Журнал',
            'fiscal-position'              => 'Финансовый Должность',
            'currency'                     => 'Валюта',
            'company'                      => 'Компания',
            'date'                         => 'Учёт Дата',
            'delivery-date'                => 'Доставка Дата',
            'amount-untaxed'               => 'Без налога Сумма',
            'amount-tax'                   => 'Налог Сумма',
            'amount-total'                 => 'Итого Сумма',
            'amount-residual'              => 'Сумма Due',
            'checked'                      => 'Checked',
            'posted-before'                => 'Проведён До',
            'is-move-sent'                 => 'Отправлено',
            'created-at'                   => 'Создано',
            'updated-at'                   => 'Обновлено',
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
                    'customer-invoice' => 'Счёт клиенту',
                    'customer'         => 'Клиент',
                    'invoice-date'     => 'Дата счёта',
                    'due-date'         => 'Срок оплаты',
                    'payment-term'     => 'Условие оплаты',
                    'journal'          => 'Журнал',
                    'currency'         => 'Валюта',
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

                        'entries' => [
                            'company'           => 'Компания',
                            'incoterm'          => 'Incoterm',
                            'incoterm-location' => 'Incoterm Место',
                            'payment-method'    => 'Оплата Method',
                            'cash-rounding'     => 'Касса Rounding Method',
                            'fiscal-position'   => 'Финансовый Должность',
                            'auto-post'         => 'Auto Post',
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
                        'currency' => 'Валюта',
                        'due-date' => 'Срок оплаты',
                        'taxes'    => 'Налоги',
                        'debit'    => 'Дебет',
                        'credit'   => 'Кредит',
                    ],
                ],
            ],
        ],
    ],

    'summary' => [
        'actions' => [
            'reconcile' => [
                'label' => 'Добавить',
            ],

            'unreconcile' => [
                'label' => 'Unlink',
            ],
        ],
    ],

];
