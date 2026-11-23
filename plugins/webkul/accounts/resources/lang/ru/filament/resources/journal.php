<?php

return [
    'form' => [
        'tabs' => [
            'journal-entries' => [
                'title' => 'Проводки',

                'field-set' => [
                    'accounting-information' => [
                        'title'  => 'Учёт Сведения',
                        'fields' => [
                            'dedicated-credit-note-sequence' => 'Dedicated Кредит-нота Нумерация',
                            'dedicated-payment-sequence'     => 'Dedicated Оплата Нумерация',
                            'sort-code-placeholder'          => 'Введите журнал код',
                            'sort-code'                      => 'Sort',
                            'currency'                       => 'Валюта',
                            'color'                          => 'Цвет',
                            'default-account'                => 'По умолчанию Счёт',
                            'profit-account'                 => 'Прибыль Счёт',
                            'loss-account'                   => 'Убыток Счёт',
                            'suspense-account'               => 'Suspense Счёт',
                            'bank-account'                   => 'Банковский счёт',
                        ],
                    ],

                    'bank-account-number' => [
                        'title' => 'Банковский счёт Номер',
                    ],
                ],
            ],

            'incoming-payments' => [
                'title'            => 'Incoming Оплаты',
                'add-action-label' => 'Добавить Строка',

                'fields' => [
                    'payment-method'             => 'Оплата Method',
                    'display-name'               => 'Display Название',
                    'account-number'             => 'Outstanding Приходы Счета',
                    'relation-notes'             => 'Relation Заметки',
                    'relation-notes-placeholder' => 'Введите любой relation подробности',
                ],
            ],

            'outgoing-payments' => [
                'title'            => 'Outgoing Оплаты',
                'add-action-label' => 'Добавить Строка',

                'fields' => [
                    'payment-method'             => 'Оплата Method',
                    'display-name'               => 'Display Название',
                    'account-number'             => 'Outstanding Оплаты Счета',
                    'relation-notes'             => 'Relation Заметки',
                    'relation-notes-placeholder' => 'Введите любой relation подробности',
                ],
            ],

            'advanced-settings' => [
                'title'  => 'Advanced Настройки',

                'fields' => [
                    'allowed-accounts'       => 'Разрешено Счета',
                    'control-access'         => 'Control Access',
                    'payment-communication'  => 'Оплата Сообщения',
                    'auto-check-on-post'     => 'Auto Чек вкл. Post',
                    'communication-type'     => 'Сообщения Тип',
                    'communication-standard' => 'Сообщения Standard',
                ],
            ],
        ],

        'general' => [
            'title' => 'Общее Сведения',

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
            'created-by' => 'Создано От',
            'status'     => 'Статус',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Журнал удалено',
                        'body'  => 'журнал есть удалено successfully.',
                    ],

                    'error' => [
                        'title' => 'Журнал deletion не удалось',
                        'body'  => 'журнал нельзя быть удалено потому что это currently в использовать.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Журнал удалено',
                        'body'  => 'журнал есть удалено successfully.',
                    ],

                    'error' => [
                        'title' => 'Журналы deletion не удалось',
                        'body'  => 'журналы нельзя быть удалено потому что они currently в использовать.',
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
                        'title'   => 'Учёт Сведения',

                        'entries' => [
                            'dedicated-credit-note-sequence' => 'Dedicated Кредит-нота Нумерация',
                            'dedicated-payment-sequence'     => 'Dedicated Оплата Нумерация',
                            'sort-code-placeholder'          => 'Введите журнал код',
                            'sort-code'                      => 'Sort',
                            'currency'                       => 'Валюта',
                            'color'                          => 'Цвет',
                            'default-account'                => 'По умолчанию Счёт',
                            'profit-account'                 => 'Прибыль Счёт',
                            'loss-account'                   => 'Убыток Счёт',
                            'suspense-account'               => 'Suspense Счёт',
                        ],
                    ],

                    'bank-account-number' => [
                        'title' => 'Банковский счёт Номер',

                        'entries' => [
                            'account-number' => 'Счёт Номер',
                        ],
                    ],
                ],
            ],

            'incoming-payments' => [
                'title' => 'Incoming Оплаты',

                'entries' => [
                    'payment-method'             => 'Оплата Method',
                    'display-name'               => 'Display Название',
                    'account-number'             => 'Outstanding Приходы Счета',
                    'relation-notes'             => 'Relation Заметки',
                    'relation-notes-placeholder' => 'Введите любой relation подробности',
                ],
            ],

            'outgoing-payments' => [
                'title' => 'Outgoing Оплаты',

                'entries' => [
                    'payment-method'             => 'Оплата Method',
                    'display-name'               => 'Display Название',
                    'account-number'             => 'Outstanding Оплаты Счета',
                    'relation-notes'             => 'Relation Заметки',
                    'relation-notes-placeholder' => 'Введите любой relation подробности',
                ],
            ],

            'advanced-settings' => [
                'title'   => 'Advanced Настройки',

                'allowed-accounts' => [
                    'title' => 'Разрешено Счета',

                    'entries' => [
                        'allowed-accounts'       => 'Разрешено Счета',
                        'control-access'         => 'Control Access',
                        'auto-check-on-post'     => 'Auto Чек вкл. Post',
                    ],
                ],

                'payment-communication'  => [
                    'title' => 'Оплата Сообщения',

                    'entries' => [
                        'communication-type'     => 'Сообщения Тип',
                        'communication-standard' => 'Сообщения Standard',
                    ],
                ],
            ],
        ],

        'general' => [
            'title' => 'Общее Сведения',

            'entries' => [
                'name'    => 'Название',
                'type'    => 'Тип',
                'company' => 'Компания',
            ],
        ],
    ],

];
