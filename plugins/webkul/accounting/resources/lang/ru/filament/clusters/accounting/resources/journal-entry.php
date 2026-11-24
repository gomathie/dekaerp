<?php

return [
    'title' => 'Проводки',

    'navigation' => [
        'title' => 'Проводки',
    ],

    'record-sub-navigation' => [
        'payment' => 'Платёж',
    ],

    'global-search' => [
        'number'   => 'Номер',
        'partner'  => 'Контакт',
        'date'     => 'Дата счёта',
        'due-date' => 'Срок оплаты счёта',
    ],

    'form' => [
        'section' => [
            'general' => [
                'title'  => 'Основное',

                'fields' => [
                    'reference'       => 'Ссылка',
                    'accounting-date' => 'Дата проводки',
                    'journal'         => 'Журнал',
                ],
            ],
        ],

        'tabs' => [
            'lines' => [
                'title' => 'Строки проводок',

                'repeater' => [
                    'title'       => 'Строки',
                    'add-item'    => 'Добавить строку',

                    'columns' => [
                        'account'                  => 'Счёт',
                        'partner'                  => 'Контакт',
                        'label'                    => 'Метка',
                        'amount-currency'          => 'Сумма (валюта)',
                        'currency'                 => 'Валюта',
                        'taxes'                    => 'Налоги',
                        'debit'                    => 'Дебет',
                        'credit'                   => 'Кредит',
                        'discount-amount-currency' => 'Сумма скидки (валюта)',
                    ],

                    'fields' => [
                        'account'                  => 'Счёт',
                        'partner'                  => 'Контакт',
                        'label'                    => 'Метка',
                        'amount-currency'          => 'Сумма (валюта)',
                        'currency'                 => 'Валюта',
                        'taxes'                    => 'Налоги',
                        'debit'                    => 'Дебет',
                        'credit'                   => 'Кредит',
                        'discount-amount-currency' => 'Сумма скидки (валюта)',
                    ],
                ],
            ],

            'other-information' => [
                'title'    => 'Прочая информация',

                'fields' => [
                    'checked'         => 'Проверено',
                    'company'         => 'Компания',
                    'fiscal-position' => 'Налоговая позиция',
                    'fiscal-position-tooltip' => 'Налоговые позиции применяются для подбора налогов и счетов в зависимости от местонахождения клиента.',
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
            'invoice-date' => 'Дата счёта',
            'date'         => 'Дата',
            'number'       => 'Номер',
            'partner'      => 'Контакт',
            'reference'    => 'Ссылка',
            'journal'      => 'Журнал',
            'company'      => 'Компания',
            'total'        => 'Итого',
            'state'        => 'Статус',
            'checked'      => 'Проверено',
        ],

        'summarizers' => [
            'total' => 'Итого',
        ],

        'groups' => [
            'partner'        => 'Контакт',
            'journal'        => 'Журнал',
            'state'          => 'Статус',
            'payment-method' => 'Способ оплаты',
            'date'           => 'Дата',
            'invoice-date'   => 'Дата счёта',
            'company'        => 'Компания',
        ],

        'filters' => [
            'number'                       => 'Номер',
            'invoice-partner-display-name' => 'Отображаемое имя контакта в счёте',
            'invoice-date'                 => 'Дата счёта',
            'invoice-due-date'             => 'Срок оплаты счёта',
            'invoice-origin'               => 'Источник счёта',
            'reference'                    => 'Ссылка',
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
                'title'   => 'Основное',
                'entries' => [
                    'number'          => 'Номер',
                    'reference'       => 'Ссылка',
                    'accounting-date' => 'Дата проводки',
                    'journal'         => 'Журнал',
                ],
            ],
        ],

        'tabs' => [
            'lines' => [
                'title' => 'Строки проводок',

                'repeater' => [
                    'entries' => [
                        'account'  => 'Счёт',
                        'partner'  => 'Контакт',
                        'label'    => 'Метка',
                        'currency' => 'Валюта',
                        'taxes'    => 'Налоги',
                        'debit'    => 'Дебет',
                        'credit'   => 'Кредит',
                    ],
                ],
            ],

            'other-information' => [
                'title' => 'Прочая информация',

                'fieldset' => [
                    'accounting' => [
                        'title' => 'Бухгалтерия',

                        'entries' => [
                            'company'         => 'Компания',
                            'fiscal-position' => 'Налоговая позиция',
                            'checked'         => 'Проверено',
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
