<?php

return [
    'title' => 'Washirika',

    'navigation' => [
        'title' => 'Washirika',
    ],

    'form' => [
        'tabs' => [
            'sales-purchases' => [
                'fieldsets' => [
                    'sales' => [
                        'title' => 'Mauzo',

                        'fields' => [
                            'sales-person'            => 'Mauzo Person',
                            'payment-terms'           => 'Masharti ya malipo',
                            'payment-method'          => 'Malipo Method',
                            'default-currency'        => 'Sarafu chaguomsingi',
                            'default-currency-helper' => 'Ankara, bili na oda za ununuzi za mhusika huyu hufunguka kwa sarafu hii. Acha wazi kutumia sarafu yako ya kawaida.',
                        ],
                    ],

                    'purchase' => [
                        'title' => 'Ununuzi',

                        'fields' => [
                            'payment-terms'  => 'Masharti ya malipo',
                            'payment-method' => 'Malipo Method',
                        ],
                    ],

                    'fiscal-information' => [
                        'title' => 'Kifedha Taarifa',

                        'fields' => [
                            'fiscal-position'    => 'Kifedha Cheo',
                        ],
                    ],
                ],
            ],

            'invoicing' => [
                'title'  => 'Invoicing',

                'fieldsets' => [
                    'customer-invoices' => [
                        'title' => 'Ankara ya mtejas',

                        'fields' => [
                            'invoice-sending-method'   => 'Ankara Sending Method',
                            'invoice-edi-format-store' => 'eInvoice Umbizo',
                            'peppol-eas'               => 'Peppol Anwani',
                            'endpoint'                 => 'Endpoint',
                        ],
                    ],

                    'accounting-entries' => [
                        'title' => 'Uhasibu Maingizo',

                        'fields' => [
                            'account-receivable' => 'Akaunti Ya kupokea',
                            'account-payable'    => 'Akaunti Ya kulipa',
                        ],
                    ],

                    'automation' => [
                        'title' => 'Automation',

                        'fields' => [
                            'auto-post-bills'                => 'Auto Post Bili',
                            'ignore-abnormal-invoice-amount' => 'Ignore Abnormal Ankara Kiasi',
                            'ignore-abnormal-invoice-date'   => 'Ignore Abnormal Tarehe ya ankara',
                        ],
                    ],
                ],
            ],

            'internal-notes' => [
                'title' => 'Ndani Maelezo',
            ],
        ],
    ],

    'infolist' => [

        'tabs' => [
            'sales-purchases' => [
                'fieldsets' => [
                    'sales' => [
                        'title' => 'Mauzo',

                        'entries' => [
                            'sales-person'   => 'Mauzo Person',
                            'payment-terms'  => 'Masharti ya malipo',
                            'payment-method' => 'Malipo Method',
                        ],
                    ],

                    'purchase' => [
                        'title' => 'Ununuzi',

                        'entries' => [
                            'payment-terms'  => 'Masharti ya malipo',
                            'payment-method' => 'Malipo Method',
                        ],
                    ],

                    'fiscal-information' => [
                        'title' => 'Kifedha Taarifa',

                        'entries' => [
                            'fiscal-position'    => 'Kifedha Cheo',
                        ],
                    ],
                ],
            ],

            'invoicing' => [
                'title'  => 'Invoicing',

                'fieldsets' => [
                    'customer-invoices' => [
                        'title' => 'Ankara ya mtejas',

                        'entries' => [
                            'invoice-sending-method'   => 'Ankara Sending Method',
                            'invoice-edi-format-store' => 'eInvoice Umbizo',
                            'peppol-eas'               => 'Peppol Anwani',
                            'endpoint'                 => 'Endpoint',
                        ],
                    ],

                    'accounting-entries' => [
                        'title' => 'Uhasibu Maingizo',

                        'entries' => [
                            'account-receivable' => 'Akaunti Ya kupokea',
                            'account-payable'    => 'Akaunti Ya kulipa',
                        ],
                    ],

                    'automation' => [
                        'title' => 'Automation',

                        'entries' => [
                            'auto-post-bills'                => 'Auto Post Bili',
                            'ignore-abnormal-invoice-amount' => 'Ignore Abnormal Ankara Kiasi',
                            'ignore-abnormal-invoice-date'   => 'Ignore Abnormal Tarehe ya ankara',
                        ],
                    ],
                ],
            ],

            'internal-notes' => [
                'title' => 'Ndani Maelezo',
            ],
        ],
    ],
];
