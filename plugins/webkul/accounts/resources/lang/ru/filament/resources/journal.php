<?php

return [
    'form' => [
        'tabs' => [
            'journal-entries' => [
                'title' => 'Проводки',

                'field-set' => [
                    'accounting-information' => [
                        'title'  => 'Учётная информация',
                        'fields' => [
                            'dedicated-credit-note-sequence' => 'Отдельная нумерация кредит-нот',
                            'dedicated-payment-sequence'     => 'Отдельная нумерация платежей',
                            'sort-code-placeholder'          => 'Введите код журнала',
                            'sort-code'                      => 'Порядок',
                            'currency'                       => 'Валюта',
                            'color'                          => 'Цвет',
                            'default-account'                => 'Счёт по умолчанию',
                            'profit-account'                 => 'Счёт прибыли',
                            'loss-account'                   => 'Счёт убытков',
                            'suspense-account'               => 'Промежуточный счёт',
                            'bank-account'                   => 'Банковский счёт',
                        ],
                    ],

                    'bank-account-number' => [
                        'title' => 'Номер банковского счёта',
                    ],
                ],
            ],

            'incoming-payments' => [
                'title'            => 'Входящие платежи',
                'add-action-label' => 'Добавить строку',

                'fields' => [
                    'payment-method'             => 'Способ оплаты',
                    'display-name'               => 'Отображаемое имя',
                    'account-number'             => 'Счета неучтённых поступлений',
                    'relation-notes'             => 'Примечания к связи',
                    'relation-notes-placeholder' => 'Введите сведения о связи',
                ],
            ],

            'outgoing-payments' => [
                'title'            => 'Исходящие платежи',
                'add-action-label' => 'Добавить строку',

                'fields' => [
                    'payment-method'             => 'Способ оплаты',
                    'display-name'               => 'Отображаемое имя',
                    'account-number'             => 'Счета неучтённых платежей',
                    'relation-notes'             => 'Примечания к связи',
                    'relation-notes-placeholder' => 'Введите сведения о связи',
                ],
            ],

            'advanced-settings' => [
                'title'  => 'Дополнительные настройки',

                'fields' => [
                    'allowed-accounts'       => 'Разрешённые счета',
                    'control-access'         => 'Контроль доступа',
                    'payment-communication'  => 'Назначение платежа',
                    'auto-check-on-post'     => 'Отмечать проверенным при проведении',
                    'communication-type'     => 'Тип назначения',
                    'communication-standard' => 'Стандарт назначения',
                ],
            ],
        ],

        'general' => [
            'title' => 'Общая информация',

            'fields' => [
                'name'    => 'Название',
                'type'    => 'Тип',
                'company' => 'Компания',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'       => 'Название',
            'type'       => 'Тип',
            'code'       => 'Код',
            'currency'   => 'Валюта',
            'created-by' => 'Создал',
            'status'     => 'Статус',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Журнал удалён',
                        'body'  => 'Журнал успешно удалён.',
                    ],

                    'error' => [
                        'title' => 'Не удалось удалить журнал',
                        'body'  => 'Журнал нельзя удалить: он сейчас используется.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Журналы удалены',
                        'body'  => 'Журналы успешно удалены.',
                    ],

                    'error' => [
                        'title' => 'Не удалось удалить журналы',
                        'body'  => 'Журналы нельзя удалить: они сейчас используются.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'tabs' => [
            'journal-entries' => [
                'title' => 'Проводки',

                'field-set' => [
                    'accounting-information' => [
                        'title'   => 'Учётная информация',

                        'entries' => [
                            'dedicated-credit-note-sequence' => 'Отдельная нумерация кредит-нот',
                            'dedicated-payment-sequence'     => 'Отдельная нумерация платежей',
                            'sort-code-placeholder'          => 'Введите код журнала',
                            'sort-code'                      => 'Порядок',
                            'currency'                       => 'Валюта',
                            'color'                          => 'Цвет',
                            'default-account'                => 'Счёт по умолчанию',
                            'profit-account'                 => 'Счёт прибыли',
                            'loss-account'                   => 'Счёт убытков',
                            'suspense-account'               => 'Промежуточный счёт',
                        ],
                    ],

                    'bank-account-number' => [
                        'title' => 'Номер банковского счёта',

                        'entries' => [
                            'account-number' => 'Номер счёта',
                        ],
                    ],
                ],
            ],

            'incoming-payments' => [
                'title' => 'Входящие платежи',

                'entries' => [
                    'payment-method'             => 'Способ оплаты',
                    'display-name'               => 'Отображаемое имя',
                    'account-number'             => 'Счета неучтённых поступлений',
                    'relation-notes'             => 'Примечания к связи',
                    'relation-notes-placeholder' => 'Введите сведения о связи',
                ],
            ],

            'outgoing-payments' => [
                'title' => 'Исходящие платежи',

                'entries' => [
                    'payment-method'             => 'Способ оплаты',
                    'display-name'               => 'Отображаемое имя',
                    'account-number'             => 'Счета неучтённых платежей',
                    'relation-notes'             => 'Примечания к связи',
                    'relation-notes-placeholder' => 'Введите сведения о связи',
                ],
            ],

            'advanced-settings' => [
                'title'   => 'Дополнительные настройки',

                'allowed-accounts' => [
                    'title' => 'Разрешённые счета',

                    'entries' => [
                        'allowed-accounts'       => 'Разрешённые счета',
                        'control-access'         => 'Контроль доступа',
                        'auto-check-on-post'     => 'Отмечать проверенным при проведении',
                    ],
                ],

                'payment-communication'  => [
                    'title' => 'Назначение платежа',

                    'entries' => [
                        'communication-type'     => 'Тип назначения',
                        'communication-standard' => 'Стандарт назначения',
                    ],
                ],
            ],
        ],

        'general' => [
            'title' => 'Общая информация',

            'entries' => [
                'name'    => 'Название',
                'type'    => 'Тип',
                'company' => 'Компания',
            ],
        ],
    ],

];
