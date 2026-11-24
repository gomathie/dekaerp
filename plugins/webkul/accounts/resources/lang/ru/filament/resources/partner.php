<?php

return [
    'title' => 'Контрагенты',

    'navigation' => [
        'title' => 'Контрагенты',
    ],

    'form' => [
        'tabs' => [
            'sales-purchases' => [
                'fieldsets' => [
                    'sales' => [
                        'title' => 'Продажи',

                        'fields' => [
                            'sales-person'            => 'Продажи Person',
                            'payment-terms'           => 'Условия оплаты',
                            'payment-method'          => 'Оплата Method',
                            'default-currency'        => 'Валюта по умолчанию',
                            'default-currency-helper' => 'Счета, счета поставщиков и заказы на закупку для этого контрагента открываются в этой валюте. Оставьте пустым, чтобы использовать обычную валюту.',
                        ],
                    ],

                    'purchase' => [
                        'title' => 'Закупка',

                        'fields' => [
                            'payment-terms'  => 'Условия оплаты',
                            'payment-method' => 'Оплата Method',
                        ],
                    ],

                    'fiscal-information' => [
                        'title' => 'Финансовый Сведения',

                        'fields' => [
                            'fiscal-position'    => 'Финансовый Должность',
                        ],
                    ],
                ],
            ],

            'invoicing' => [
                'title'  => 'Invoicing',

                'fieldsets' => [
                    'customer-invoices' => [
                        'title' => 'Счёт клиентуs',

                        'fields' => [
                            'invoice-sending-method'   => 'Счёт Sending Method',
                            'invoice-edi-format-store' => 'eInvoice Формат',
                            'peppol-eas'               => 'Peppol Адрес',
                            'endpoint'                 => 'Endpoint',
                        ],
                    ],

                    'accounting-entries' => [
                        'title' => 'Учёт Проводки',

                        'fields' => [
                            'account-receivable' => 'Счёт К получению',
                            'account-payable'    => 'Счёт К оплате',
                        ],
                    ],

                    'automation' => [
                        'title' => 'Automation',

                        'fields' => [
                            'auto-post-bills'                => 'Auto Post Счета поставщиков',
                            'ignore-abnormal-invoice-amount' => 'Ignore Abnormal Счёт Сумма',
                            'ignore-abnormal-invoice-date'   => 'Ignore Abnormal Дата счёта',
                        ],
                    ],
                ],
            ],

            'internal-notes' => [
                'title' => 'Внутренний Заметки',
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
                            'sales-person'   => 'Продажи Person',
                            'payment-terms'  => 'Условия оплаты',
                            'payment-method' => 'Оплата Method',
                        ],
                    ],

                    'purchase' => [
                        'title' => 'Закупка',

                        'entries' => [
                            'payment-terms'  => 'Условия оплаты',
                            'payment-method' => 'Оплата Method',
                        ],
                    ],

                    'fiscal-information' => [
                        'title' => 'Финансовый Сведения',

                        'entries' => [
                            'fiscal-position'    => 'Финансовый Должность',
                        ],
                    ],
                ],
            ],

            'invoicing' => [
                'title'  => 'Invoicing',

                'fieldsets' => [
                    'customer-invoices' => [
                        'title' => 'Счёт клиентуs',

                        'entries' => [
                            'invoice-sending-method'   => 'Счёт Sending Method',
                            'invoice-edi-format-store' => 'eInvoice Формат',
                            'peppol-eas'               => 'Peppol Адрес',
                            'endpoint'                 => 'Endpoint',
                        ],
                    ],

                    'accounting-entries' => [
                        'title' => 'Учёт Проводки',

                        'entries' => [
                            'account-receivable' => 'Счёт К получению',
                            'account-payable'    => 'Счёт К оплате',
                        ],
                    ],

                    'automation' => [
                        'title' => 'Automation',

                        'entries' => [
                            'auto-post-bills'                => 'Auto Post Счета поставщиков',
                            'ignore-abnormal-invoice-amount' => 'Ignore Abnormal Счёт Сумма',
                            'ignore-abnormal-invoice-date'   => 'Ignore Abnormal Дата счёта',
                        ],
                    ],
                ],
            ],

            'internal-notes' => [
                'title' => 'Внутренний Заметки',
            ],
        ],
    ],
];
