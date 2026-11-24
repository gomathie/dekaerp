<?php

return [
    'title' => 'Платёж',

    'navigation' => [
        'title' => 'Платежи',
        'group' => 'Счета',
    ],

    'global-search' => [
        'partner' => 'Контакт',
        'amount'  => 'Сумма',
        'date'    => 'Дата',
    ],

    'form' => [
        'sections' => [
            'fields' => [
                'payment-type'          => 'Тип платежа',
                'memo'                  => 'Комментарий',
                'date'                  => 'Дата',
                'amount'                => 'Сумма',
                'currency'              => 'Валюта',
                'payment-method'        => 'Способ оплаты',
                'customer'              => 'Клиент',
                'vendor'                => 'Поставщик',
                'journal'               => 'Журнал',
                'customer-bank-account' => 'Банковский счёт клиента',
                'vendor-bank-account'   => 'Банковский счёт поставщика',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'            => 'Название',
            'date'            => 'Дата',
            'journal'         => 'Журнал',
            'payment-method'  => 'Способ оплаты',
            'partner'         => 'Контакт',
            'amount-currency' => 'Сумма (в валюте)',
            'amount'          => 'Сумма',
            'state'           => 'Статус',
            'company'         => 'Компания',
            'currency'        => 'Валюта',
            'created-by'      => 'Создал',
            'invoices'        => 'Счёт',
            'unused'          => 'Неиспользованная сумма',
        ],

        'summaries' => [
            'total' => 'Итого',
        ],

        'groups' => [
            'name'                             => 'Название',
            'company'                          => 'Компания',
            'journal'                          => 'Журнал',
            'partner'                          => 'Контакт',
            'payment-method-line'              => 'Способ оплаты (строка)',
            'payment-method'                   => 'Способ оплаты',
            'partner-bank-account'             => 'Банковский счёт контакта',
            'created-at'                       => 'Создано',
            'updated-at'                       => 'Обновлено',
        ],

        'filters' => [
            'company'                          => 'Компания',
            'journal'                          => 'Журнал',
            'customer-bank-account'            => 'Банковский счёт клиента',
            'payment-method'                   => 'Способ оплаты',
            'currency'                         => 'Валюта',
            'partner'                          => 'Контакт',
            'payment-method-line'              => 'Способ оплаты (строка)',
            'created-at'                       => 'Создано',
            'updated-at'                       => 'Обновлено',
            'year'                             => 'Год',
            'month'                            => 'Месяц',
            'any-year'                         => 'Любой год',
            'whole-year'                       => 'Весь год',
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
        'sections' => [
            'payment-information' => [
                'title'   => 'Платёжная информация',
                'entries' => [
                    'state'                 => 'Статус',
                    'vendor'                => 'Поставщик',
                    'customer'              => 'Клиент',
                    'payment-type'          => 'Тип платежа',
                    'journal'               => 'Журнал',
                    'customer-bank-account' => 'Банковский счёт клиента',
                    'vendor-bank-account'   => 'Банковский счёт поставщика',
                    'amount'                => 'Сумма',
                    'payment-method'        => 'Способ оплаты',
                    'date'                  => 'Дата',
                    'memo'                  => 'Комментарий',
                ],
            ],
        ],
    ],

];
