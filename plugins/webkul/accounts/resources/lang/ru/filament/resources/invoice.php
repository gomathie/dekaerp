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
                    'customer-invoice' => 'Счёт клиента',
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
                'title' => 'Нет курса обмена',
                'body'  => 'Нет курса обмена между :from и :to, поэтому цены не пересчитаны. Добавьте курс в разделе Конфигурация → Валюты или введите цены вручную.',
            ],
        ],

        'tabs' => [
            'invoice-lines' => [
                'title' => 'Строки счёта',

                'repeater' => [
                    'products' => [
                        'title'       => 'Товары',
                        'add-product' => 'Добавить товар',
                        'price-hint'  => 'Цена за единицу, изменённая здесь, действует только для этого счёта. Цена товара не меняется.',

                        'columns' => [
                            'product'             => 'Товар',
                            'quantity'            => 'Количество',
                            'unit'                => 'Ед. изм.',
                            'taxes'               => 'Налоги',
                            'discount-percentage' => 'Скидка',
                            'unit-price'          => 'Цена за ед.',
                            'sub-total'           => 'Подытог',
                        ],

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
                            'company'                 => 'Компания',
                            'incoterm'                => 'Инкотермс',
                            'incoterm-location'       => 'Место по Инкотермс',
                            'fiscal-position'         => 'Налоговая позиция',
                            'fiscal-position-tooltip' => 'Налоговые позиции адаптируют налоги и счета к местонахождению клиента.',
                            'cash-rounding'           => 'Способ округления наличных',
                            'cash-rounding-tooltip'   => 'Наименьшая денежная единица валюты, которую можно оплатить наличными.',
                            'payment-method'          => 'Способ оплаты',
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
            'created-by'       => 'Создал',
            'customer'         => 'Клиент',
            'invoice-date'     => 'Дата счёта',
            'checked'          => 'Проверен',
            'accounting-date'  => 'Учёт',
            'due-date'         => 'Срок оплаты',
            'source-document'  => 'Исходный документ',
            'reference'        => 'Ссылка',
            'sales-person'     => 'Менеджер по продажам',
            'tax-excluded'     => 'Без налога',
            'tax'              => 'Налог',
            'total'            => 'Итого',
            'amount-paid'      => 'Оплачено',
            'amount-due'       => 'К оплате',
            'invoice-currency' => 'Валюта счёта',
        ],

        'summarizers' => [
            'total' => 'Итого',
        ],

        'groups' => [
            'name'                         => 'Название',
            'invoice-partner-display-name' => 'Отображаемое имя контакта',
            'invoice-date'                 => 'Дата счёта',
            'checked'                      => 'Проверен',
            'date'                         => 'Дата',
            'invoice-due-date'             => 'Срок оплаты счёта',
            'invoice-origin'               => 'Источник счёта',
            'sales-person'                 => 'Менеджер по продажам',
            'currency'                     => 'Валюта',
            'created-at'                   => 'Создано',
            'updated-at'                   => 'Обновлено',
        ],

        'filters' => [
            'number'                       => 'Номер',
            'invoice-partner-display-name' => 'Отображаемое имя контакта',
            'invoice-date'                 => 'Дата счёта',
            'invoice-due-date'             => 'Срок оплаты счёта',
            'invoice-origin'               => 'Источник счёта',
            'reference'                    => 'Ссылка',
            'payment-reference'            => 'Назначение платежа',
            'narration'                    => 'Комментарий',
            'partner'                      => 'Контакт',
            'journal'                      => 'Журнал',
            'fiscal-position'              => 'Налоговая позиция',
            'currency'                     => 'Валюта',
            'company'                      => 'Компания',
            'date'                         => 'Дата учёта',
            'delivery-date'                => 'Дата отгрузки',
            'amount-untaxed'               => 'Сумма без налога',
            'amount-tax'                   => 'Сумма налога',
            'amount-total'                 => 'Общая сумма',
            'amount-residual'              => 'К оплате',
            'checked'                      => 'Проверен',
            'posted-before'                => 'Ранее проводился',
            'is-move-sent'                 => 'Отправлен',
            'created-at'                   => 'Создано',
            'updated-at'                   => 'Обновлено',
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
                    'body'  => 'Платежи успешно удалены.',
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
                    'customer-invoice' => 'Счёт клиента',
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

                        'entries' => [
                            'company'           => 'Компания',
                            'incoterm'          => 'Инкотермс',
                            'incoterm-location' => 'Место по Инкотермс',
                            'payment-method'    => 'Способ оплаты',
                            'cash-rounding'     => 'Способ округления наличных',
                            'fiscal-position'   => 'Налоговая позиция',
                            'auto-post'         => 'Автопроведение',
                            'checked'           => 'Проверен',
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
                'label' => 'Отвязать',
            ],
        ],
    ],

];
