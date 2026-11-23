<?php

return [
    'table' => [
        'columns' => [
            'on-hand'    => 'Вкл. Вручную',
            'forecasted' => 'Forecasted',
        ],
    ],

    'navigation' => [
        'title' => 'Товары',
        'group' => 'Склад',
    ],

    'form' => [
        'sections' => [
            'inventory' => [
                'title' => 'Склад',

                'fieldsets' => [
                    'tracking' => [
                        'title' => 'Tracking',

                        'fields' => [
                            'track-inventory'              => 'Track Склад',
                            'track-inventory-hint-tooltip' => 'storable товар one тот requires склад management..',
                            'track-by'                     => 'Track От',
                            'expiration-date'              => 'Expiration Дата',
                            'expiration-date-hint-tooltip' => 'Если выбрано, вы можно specify expiration dates для товар и его associated партия/серийный номер numbers.',
                        ],
                    ],

                    'operation' => [
                        'title' => 'Операции',

                        'fields' => [
                            'routes'              => 'Маршруты',
                            'routes-hint-tooltip' => 'Based вкл. installed модули, этот настройка allows вы до define product\'s маршрут, такой как purchasing, производство, или replenishing вкл. заказ.',
                        ],
                    ],

                    'logistics' => [
                        'title' => 'Логистика',

                        'fields' => [
                            'responsible'              => 'Responsible',
                            'responsible-hint-tooltip' => 'Доставка lead time (в дни) represents promised duration между заказ на продажу confirmation и товар доставка.',
                            'weight'                   => 'Вес',
                            'volume'                   => 'Объём',
                            'sale-delay'               => 'Клиент Lead Time (Дни)',
                            'sale-delay-hint-tooltip'  => 'Доставка lead time (в дни) represents promised duration между заказ на продажу confirmation и товар доставка.',
                        ],
                    ],

                    'traceability' => [
                        'title' => 'Traceability',

                        'fields' => [
                            'expiration-date'               => 'Expiration Дата (Дни)',
                            'expiration-date-hint-tooltip'  => 'Если выбрано, вы можно задать expiration dates для товар и его associated партия/серийный номер numbers.',
                            'best-before-date'              => 'Best До Дата (Дни)',
                            'best-before-date-hint-tooltip' => 'номер дни до expiration дата когда товар begins до deteriorate, though это всё ещё safe до использовать. Этот calculated based вкл. партия/серийный номер номер.',
                            'removal-date'                  => 'Removal Дата (Дни)',
                            'removal-date-hint-tooltip'     => 'номер дни до expiration дата когда товар следует быть removed от запас. Этот calculated based вкл. партия/серийный номер номер.',
                            'alert-date'                    => 'Alert Дата (Дни)',
                            'alert-date-hint-tooltip'       => 'номер дни до expiration дата когда alert следует быть triggered для партия/серийный номер номер. Этот calculated based вкл. партия/серийный номер номер.',
                        ],
                    ],
                ],
            ],

            'additional' => [
                'title' => 'Additional',
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'inventory' => [
                'title' => 'Склад',

                'entries' => [
                ],

                'fieldsets' => [
                    'tracking' => [
                        'title' => 'Tracking',

                        'entries' => [
                            'track-inventory' => 'Track Склад',
                            'track-by'        => 'Track От',
                            'expiration-date' => 'Expiration Дата',
                        ],
                    ],

                    'operation' => [
                        'title' => 'Операции',

                        'entries' => [
                            'routes' => 'Маршруты',
                        ],
                    ],

                    'logistics' => [
                        'title' => 'Логистика',

                        'entries' => [
                            'responsible' => 'Responsible',
                            'weight'      => 'Вес',
                            'volume'      => 'Объём',
                            'sale-delay'  => 'Клиент Lead Time (Дни)',
                        ],
                    ],

                    'traceability' => [
                        'title' => 'Traceability',

                        'entries' => [
                            'expiration-date'  => 'Expiration Дата (Дни)',
                            'best-before-date' => 'Best До Дата (Дни)',
                            'removal-date'     => 'Removal Дата (Дни)',
                            'alert-date'       => 'Alert Дата (Дни)',
                        ],
                    ],
                ],
            ],
        ],
    ],
];
