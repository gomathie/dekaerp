<?php

return [
    'title' => 'Оплата',

    'navigation' => [
        'title' => 'Оплаты',
        'group' => 'Счета',
    ],

    'global-search' => [
        'partner' => 'Контрагент',
        'amount'  => 'Сумма',
        'date'    => 'Дата',
    ],

    'form' => [
        'sections' => [
            'fields' => [
                'payment-type'          => 'Оплата Тип',
                'memo'                  => 'Memo',
                'date'                  => 'Дата',
                'amount'                => 'Сумма',
                'currency'              => 'Валюта',
                'payment-method'        => 'Оплата Method',
                'customer'              => 'Клиент',
                'vendor'                => 'Поставщик',
                'journal'               => 'Журнал',
                'customer-bank-account' => 'Клиент Банковский счёт',
                'vendor-bank-account'   => 'Поставщик Банковский счёт',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'            => 'Название',
            'date'            => 'Дата',
            'journal'         => 'Журнал',
            'payment-method'  => 'Оплата Method',
            'partner'         => 'Контрагент',
            'amount-currency' => 'Сумма (Валюта)',
            'amount'          => 'Сумма',
            'state'           => 'Состояние',
            'company'         => 'Компания',
            'currency'        => 'Валюта',
            'created-by'      => 'Создано От',
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
            'partner'                          => 'Контрагент',
            'payment-method-line'              => 'Оплата Method Строка',
            'payment-method'                   => 'Оплата Method',
            'partner-bank-account'             => 'Контрагент Банковский счёт',
            'created-at'                       => 'Создано',
            'updated-at'                       => 'Обновлено',
        ],

        'filters' => [
            'company'                          => 'Компания',
            'journal'                          => 'Журнал',
            'customer-bank-account'            => 'Клиент Банковский счёт',
            'payment-method'                   => 'Оплата Method',
            'currency'                         => 'Валюта',
            'partner'                          => 'Контрагент',
            'payment-method-line'              => 'Оплата Method Строка',
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
        'sections' => [
            'payment-information' => [
                'title'   => 'Оплата Сведения',
                'entries' => [
                    'state'                 => 'Состояние',
                    'vendor'                => 'Поставщик',
                    'customer'              => 'Клиент',
                    'payment-type'          => 'Оплата Тип',
                    'journal'               => 'Журнал',
                    'customer-bank-account' => 'Клиент Банковский счёт',
                    'vendor-bank-account'   => 'Поставщик Банковский счёт',
                    'amount'                => 'Сумма',
                    'payment-method'        => 'Оплата Method',
                    'date'                  => 'Дата',
                    'memo'                  => 'Memo',
                ],
            ],
        ],
    ],

];
