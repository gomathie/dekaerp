<?php

return [
    'title' => 'Проводки',

    'navigation' => [
        'title' => 'Проводки',
    ],

    'record-sub-navigation' => [
        'payment' => 'Оплата',
    ],

    'global-search' => [
        'number'   => 'Номер',
        'partner'  => 'Контрагент',
        'date'     => 'Дата счёта',
        'due-date' => 'Счёт Срок оплаты',
    ],

    'form' => [
        'section' => [
            'general' => [
                'title'  => 'Общее',

                'fields' => [
                    'reference'       => 'Ссылка',
                    'accounting-date' => 'Учёт Дата',
                    'journal'         => 'Журнал',
                ],
            ],
        ],

        'tabs' => [
            'lines' => [
                'title' => 'Журнал Позиции',

                'repeater' => [
                    'title'       => 'Позиции',
                    'add-item'    => 'Добавить Позиция',

                    'columns' => [
                        'account'                  => 'Счёт',
                        'partner'                  => 'Контрагент',
                        'label'                    => 'Подпись',
                        'amount-currency'          => 'Сумма (Валюта)',
                        'currency'                 => 'Валюта',
                        'taxes'                    => 'Налоги',
                        'debit'                    => 'Дебет',
                        'credit'                   => 'Кредит',
                        'discount-amount-currency' => 'Скидка Сумма (Валюта)',
                    ],

                    'fields' => [
                        'account'                  => 'Счёт',
                        'partner'                  => 'Контрагент',
                        'label'                    => 'Подпись',
                        'amount-currency'          => 'Сумма (Валюта)',
                        'currency'                 => 'Валюта',
                        'taxes'                    => 'Налоги',
                        'debit'                    => 'Дебет',
                        'credit'                   => 'Кредит',
                        'discount-amount-currency' => 'Скидка Сумма (Валюта)',
                    ],
                ],
            ],

            'other-information' => [
                'title'    => 'Другое Сведения',

                'fields' => [
                    'checked'         => 'Checked',
                    'company'         => 'Компания',
                    'fiscal-position' => 'Финансовый Должность',
                    'fiscal-position-tooltip' => 'Финансовый positions используется до adapt налоги и счета based вкл. клиент место.',
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
            'invoice-date' => 'Дата счёта',
            'date'         => 'Дата',
            'number'       => 'Номер',
            'partner'      => 'Контрагент',
            'reference'    => 'Ссылка',
            'journal'      => 'Журнал',
            'company'      => 'Компания',
            'total'        => 'Итого',
            'state'        => 'Состояние',
            'checked'      => 'Checked',
        ],

        'summarizers' => [
            'total' => 'Итого',
        ],

        'groups' => [
            'partner'        => 'Контрагент',
            'journal'        => 'Журнал',
            'state'          => 'Состояние',
            'payment-method' => 'Оплата Method',
            'date'           => 'Дата',
            'invoice-date'   => 'Дата счёта',
            'company'        => 'Компания',
        ],

        'filters' => [
            'number'                       => 'Номер',
            'invoice-partner-display-name' => 'Счёт Контрагент Display Название',
            'invoice-date'                 => 'Дата счёта',
            'invoice-due-date'             => 'Счёт Срок оплаты',
            'invoice-origin'               => 'Счёт Происхождение',
            'reference'                    => 'Ссылка',
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
                    'number'          => 'Номер',
                    'reference'       => 'Ссылка',
                    'accounting-date' => 'Учёт Дата',
                    'journal'         => 'Журнал',
                ],
            ],
        ],

        'tabs' => [
            'lines' => [
                'title' => 'Журнал Позиции',

                'repeater' => [
                    'entries' => [
                        'account'  => 'Счёт',
                        'partner'  => 'Контрагент',
                        'label'    => 'Подпись',
                        'currency' => 'Валюта',
                        'taxes'    => 'Налоги',
                        'debit'    => 'Дебет',
                        'credit'   => 'Кредит',
                    ],
                ],
            ],

            'other-information' => [
                'title' => 'Другое Сведения',

                'fieldset' => [
                    'accounting' => [
                        'title' => 'Учёт',

                        'entries' => [
                            'company'         => 'Компания',
                            'fiscal-position' => 'Финансовый Должность',
                            'checked'         => 'Checked',
                        ],
                    ],
                ],
            ],

            'term-and-conditions' => [
                'title' => 'Term & Conditions',
            ],
        ],
    ],

];
