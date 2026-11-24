<?php

return [
    'title' => 'Контакты',

    'navigation' => [
        'title' => 'Контакты',
    ],

    'form' => [
        'tabs' => [
            'sales-purchases' => [
                'fieldsets' => [
                    'sales' => [
                        'title' => 'Продажи',

                        'fields' => [
                            'sales-person'            => 'Менеджер по продажам',
                            'payment-terms'           => 'Условия оплаты',
                            'payment-method'          => 'Способ оплаты',
                            'default-currency'        => 'Валюта по умолчанию',
                            'default-currency-helper' => 'Счета, счета поставщиков и заказы на закупку для этого контакта открываются в этой валюте. Оставьте пустым, чтобы использовать обычную валюту.',
                        ],
                    ],

                    'purchase' => [
                        'title' => 'Закупки',

                        'fields' => [
                            'payment-terms'  => 'Условия оплаты',
                            'payment-method' => 'Способ оплаты',
                        ],
                    ],

                    'fiscal-information' => [
                        'title' => 'Налоговые сведения',

                        'fields' => [
                            'fiscal-position'    => 'Налоговая позиция',
                        ],
                    ],
                ],
            ],

            'invoicing' => [
                'title'  => 'Выставление счетов',

                'fieldsets' => [
                    'customer-invoices' => [
                        'title' => 'Счета клиентов',

                        'fields' => [
                            'invoice-sending-method'   => 'Способ отправки счетов',
                            'invoice-edi-format-store' => 'Формат электронного счёта',
                            'peppol-eas'               => 'Адрес Peppol',
                            'endpoint'                 => 'Конечная точка',
                        ],
                    ],

                    'accounting-entries' => [
                        'title' => 'Учётные проводки',

                        'fields' => [
                            'account-receivable' => 'Счёт дебиторской задолженности',
                            'account-payable'    => 'Счёт кредиторской задолженности',
                        ],
                    ],

                    'automation' => [
                        'title' => 'Автоматизация',

                        'fields' => [
                            'auto-post-bills'                => 'Автопроведение счетов поставщиков',
                            'ignore-abnormal-invoice-amount' => 'Не проверять необычную сумму счёта',
                            'ignore-abnormal-invoice-date'   => 'Не проверять необычную дату счёта',
                        ],
                    ],
                ],
            ],

            'internal-notes' => [
                'title' => 'Внутренние примечания',
            ],
        ],
    ],

    'infolist' => [

        'tabs' => [
            'sales-purchases' => [
                'fieldsets' => [
                    'sales' => [
                        'title' => 'Продажи',

                        'entries' => [
                            'sales-person'   => 'Менеджер по продажам',
                            'payment-terms'  => 'Условия оплаты',
                            'payment-method' => 'Способ оплаты',
                        ],
                    ],

                    'purchase' => [
                        'title' => 'Закупки',

                        'entries' => [
                            'payment-terms'  => 'Условия оплаты',
                            'payment-method' => 'Способ оплаты',
                        ],
                    ],

                    'fiscal-information' => [
                        'title' => 'Налоговые сведения',

                        'entries' => [
                            'fiscal-position'    => 'Налоговая позиция',
                        ],
                    ],
                ],
            ],

            'invoicing' => [
                'title'  => 'Выставление счетов',

                'fieldsets' => [
                    'customer-invoices' => [
                        'title' => 'Счета клиентов',

                        'entries' => [
                            'invoice-sending-method'   => 'Способ отправки счетов',
                            'invoice-edi-format-store' => 'Формат электронного счёта',
                            'peppol-eas'               => 'Адрес Peppol',
                            'endpoint'                 => 'Конечная точка',
                        ],
                    ],

                    'accounting-entries' => [
                        'title' => 'Учётные проводки',

                        'entries' => [
                            'account-receivable' => 'Счёт дебиторской задолженности',
                            'account-payable'    => 'Счёт кредиторской задолженности',
                        ],
                    ],

                    'automation' => [
                        'title' => 'Автоматизация',

                        'entries' => [
                            'auto-post-bills'                => 'Автопроведение счетов поставщиков',
                            'ignore-abnormal-invoice-amount' => 'Не проверять необычную сумму счёта',
                            'ignore-abnormal-invoice-date'   => 'Не проверять необычную дату счёта',
                        ],
                    ],
                ],
            ],

            'internal-notes' => [
                'title' => 'Внутренние примечания',
            ],
        ],
    ],
];
