<?php

return [
    'title' => 'Счёт поставщика',

    'navigation' => [
        'title' => 'Счета поставщиков',
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
                    'bill-date'         => 'Дата счёта',
                    'bill-reference'    => 'Номер счёта поставщика',
                    'accounting-date'   => 'Дата учёта',
                    'payment-reference' => 'Назначение платежа',
                    'recipient-bank'    => 'Банк получателя',
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
                        'add-product' => 'Добавить позицию',

                        'columns' => [
                            'product'             => 'Товар / услуга',
                            'quantity'            => 'Кол-во',
                            'unit'                => 'Ед. изм.',
                            'taxes'               => 'Налоги',
                            'discount-percentage' => 'Скидка, %',
                            'unit-price'          => 'Цена за ед.',
                            'sub-total'           => 'Подытог',
                        ],

                        'fields' => [
                            'product'             => 'Товар / услуга',
                            'quantity'            => 'Кол-во',
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
                            'company'                 => 'Компания',
                            'incoterm'                => 'Инкотермс',
                            'incoterm-location'       => 'Место по Инкотермс',
                            'payment-method'          => 'Способ оплаты',
                            'fiscal-position'         => 'Налоговая позиция',
                            'fiscal-position-tooltip' => 'Налоговые позиции адаптируют налоги и счета к местонахождению клиента.',
                            'cash-rounding'           => 'Способ округления наличных',
                            'cash-rounding-tooltip'   => 'Наименьшая денежная единица валюты, которую можно оплатить наличными.',
                            'auto-post'               => 'Автопроведение',
                            'checked'                 => 'Проверен',
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
        'total'   => 'Итого',
        'columns' => [
            'number'           => 'Номер',
            'state'            => 'Статус',
            'customer'         => 'Клиент',
            'vendor'           => 'Поставщик',
            'bill-date'        => 'Дата счёта',
            'checked'          => 'Проверен',
            'accounting-date'  => 'Учёт',
            'due-date'         => 'Срок оплаты',
            'source-document'  => 'Исходный документ',
            'reference'        => 'Ссылка',
            'sales-person'     => 'Менеджер по продажам',
            'tax-excluded'     => 'Без налога',
            'tax'              => 'Налог',
            'total'            => 'Итого',
            'amount-due'       => 'К оплате',
            'amount-paid'      => 'Оплачено',
            'bill-currency'    => 'Валюта счёта',
        ],

        'summarizers' => [
            'total' => 'Итого',
        ],

        'groups' => [
            'name'                         => 'Название',
            'bill-partner-display-name'    => 'Отображаемое имя контакта',
            'bill-date'                    => 'Дата счёта',
            'checked'                      => 'Проверен',
            'date'                         => 'Дата',
            'bill-due-date'                => 'Срок оплаты счёта',
            'bill-origin'                  => 'Источник счёта',
            'sales-person'                 => 'Менеджер по продажам',
            'currency'                     => 'Валюта',
            'created-at'                   => 'Создано',
            'updated-at'                   => 'Обновлено',
        ],

        'filters' => [
            'number'                    => 'Номер',
            'bill-partner-display-name' => 'Отображаемое имя контакта',
            'bill-date'                 => 'Дата счёта',
            'bill-due-date'             => 'Срок оплаты счёта',
            'bill-origin'               => 'Источник счёта',
            'reference'                 => 'Ссылка',
            'payment-reference'         => 'Назначение платежа',
            'narration'                 => 'Комментарий',
            'partner'                   => 'Контакт',
            'journal'                   => 'Журнал',
            'fiscal-position'           => 'Налоговая позиция',
            'currency'                  => 'Валюта',
            'company'                   => 'Компания',
            'date'                      => 'Дата учёта',
            'delivery-date'             => 'Дата отгрузки',
            'amount-untaxed'            => 'Сумма без налога',
            'amount-tax'                => 'Сумма налога',
            'amount-total'              => 'Общая сумма',
            'amount-residual'           => 'К оплате',
            'checked'                   => 'Проверен',
            'posted-before'             => 'Ранее проводился',
            'is-move-sent'              => 'Отправлен',
            'created-at'                => 'Создано',
            'updated-at'                => 'Обновлено',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Платёж удалён',
                    'body'  => 'Платёж успешно удалён.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Платежи удалены',
                    'body'  => 'Счета поставщиков удалены.',
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
                    'vendor-invoice'    => 'Счёт поставщика',
                    'vendor'            => 'Поставщик',
                    'bill-date'         => 'Дата счёта',
                    'bill-reference'    => 'Номер счёта поставщика',
                    'accounting-date'   => 'Дата учёта',
                    'payment-reference' => 'Назначение платежа',
                    'recipient-bank'    => 'Банк получателя',
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
                        'add-product' => 'Добавить позицию',

                        'entries' => [
                            'product'             => 'Товар / услуга',
                            'quantity'            => 'Кол-во',
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
                            'company'           => 'Компания',
                            'incoterm'          => 'Инкотермс',
                            'incoterm-location' => 'Место по Инкотермс',
                            'payment-method'    => 'Способ оплаты',
                            'checked'           => 'Проверен',
                            'fiscal-position'   => 'Налоговая позиция',
                            'cash-rounding'     => 'Способ округления наличных',
                        ],
                    ],
                ],
            ],

            'term-and-conditions' => [
                'title' => 'Условия',
            ],

            'journal-items' => [
                'title' => 'Строки журнала',

                'repeater' => [
                    'entries' => [
                        'account'  => 'Счёт',
                        'partner'  => 'Контакт',
                        'label'    => 'Описание',
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
