<?php

return [
    'title' => 'Wahusika',

    'navigation' => [
        'title' => 'Wahusika',
    ],

    'form' => [
        'tabs' => [
            'sales-purchases' => [
                'fieldsets' => [
                    'sales' => [
                        'title' => 'Mauzo',

                        'fields' => [
                            'sales-person'            => 'Muuzaji',
                            'payment-terms'           => 'Masharti ya malipo',
                            'payment-method'          => 'Njia ya malipo',
                            'default-currency'        => 'Sarafu chaguo-msingi',
                            'default-currency-helper' => 'Ankara, bili na oda za ununuzi za mhusika huyu hufunguliwa kwa sarafu hii. Acha wazi ili kutumia sarafu yako ya kawaida.',
                        ],
                    ],

                    'purchase' => [
                        'title' => 'Ununuzi',

                        'fields' => [
                            'payment-terms'  => 'Masharti ya malipo',
                            'payment-method' => 'Njia ya malipo',
                        ],
                    ],

                    'fiscal-information' => [
                        'title' => 'Taarifa za kodi',

                        'fields' => [
                            'fiscal-position'    => 'Nafasi ya kodi',
                        ],
                    ],
                ],
            ],

            'invoicing' => [
                'title'  => 'Utoaji wa ankara',

                'fieldsets' => [
                    'customer-invoices' => [
                        'title' => 'Ankara za wateja',

                        'fields' => [
                            'invoice-sending-method'   => 'Njia ya kutuma ankara',
                            'invoice-edi-format-store' => 'Muundo wa ankara ya kielektroniki',
                            'peppol-eas'               => 'Anwani ya Peppol',
                            'endpoint'                 => 'Sehemu ya mwisho',
                        ],
                    ],

                    'accounting-entries' => [
                        'title' => 'Maingizo ya uhasibu',

                        'fields' => [
                            'account-receivable' => 'Akaunti ya zinazodaiwa',
                            'account-payable'    => 'Akaunti ya zinazolipwa',
                        ],
                    ],

                    'automation' => [
                        'title' => 'Uendeshaji otomatiki',

                        'fields' => [
                            'auto-post-bills'                => 'Rekodi bili kiotomatiki',
                            'ignore-abnormal-invoice-amount' => 'Puuza kiasi kisicho cha kawaida cha ankara',
                            'ignore-abnormal-invoice-date'   => 'Puuza tarehe isiyo ya kawaida ya ankara',
                        ],
                    ],
                ],
            ],

            'internal-notes' => [
                'title' => 'Vidokezo vya ndani',
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
                            'sales-person'   => 'Muuzaji',
                            'payment-terms'  => 'Masharti ya malipo',
                            'payment-method' => 'Njia ya malipo',
                        ],
                    ],

                    'purchase' => [
                        'title' => 'Ununuzi',

                        'entries' => [
                            'payment-terms'  => 'Masharti ya malipo',
                            'payment-method' => 'Njia ya malipo',
                        ],
                    ],

                    'fiscal-information' => [
                        'title' => 'Taarifa za kodi',

                        'entries' => [
                            'fiscal-position'    => 'Nafasi ya kodi',
                        ],
                    ],
                ],
            ],

            'invoicing' => [
                'title'  => 'Utoaji wa ankara',

                'fieldsets' => [
                    'customer-invoices' => [
                        'title' => 'Ankara za wateja',

                        'entries' => [
                            'invoice-sending-method'   => 'Njia ya kutuma ankara',
                            'invoice-edi-format-store' => 'Muundo wa ankara ya kielektroniki',
                            'peppol-eas'               => 'Anwani ya Peppol',
                            'endpoint'                 => 'Sehemu ya mwisho',
                        ],
                    ],

                    'accounting-entries' => [
                        'title' => 'Maingizo ya uhasibu',

                        'entries' => [
                            'account-receivable' => 'Akaunti ya zinazodaiwa',
                            'account-payable'    => 'Akaunti ya zinazolipwa',
                        ],
                    ],

                    'automation' => [
                        'title' => 'Uendeshaji otomatiki',

                        'entries' => [
                            'auto-post-bills'                => 'Rekodi bili kiotomatiki',
                            'ignore-abnormal-invoice-amount' => 'Puuza kiasi kisicho cha kawaida cha ankara',
                            'ignore-abnormal-invoice-date'   => 'Puuza tarehe isiyo ya kawaida ya ankara',
                        ],
                    ],
                ],
            ],

            'internal-notes' => [
                'title' => 'Vidokezo vya ndani',
            ],
        ],
    ],
];
