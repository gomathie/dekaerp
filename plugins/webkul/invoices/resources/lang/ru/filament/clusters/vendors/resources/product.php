<?php

return [
    'navigation' => [
        'title' => 'Товары',
        'group' => 'Склад',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Общее',

                'fields' => [
                    'name'             => 'Название',
                    'name-placeholder' => 'eg. T-shirt',
                    'description'      => 'Описание',
                    'tags'             => 'Метки',
                    'sales'            => 'Продажи',
                    'purchase'         => 'Закупка',
                ],
            ],

            'invoice-policy' => [
                'title'            => 'Счёт Policy',
                'ordered-policy'   => 'Вы можно счёт goods до они delivered.',
                'delivered-policy' => 'Счёт после доставка, based вкл. quantities delivered, не ordered.',
            ],

            'images' => [
                'title' => 'Images',
            ],

            'settings' => [
                'title' => 'Настройки',

                'fields' => [
                    'type'      => 'Тип',
                    'reference' => 'Ссылка',
                    'barcode'   => 'Штрихкод',
                    'category'  => 'Категория',
                    'company'   => 'Компания',
                ],
            ],

            'category-and-tags' => [
                'title' => 'Категория & Метки',

                'fields' => [
                    'category' => 'Категория',
                    'tags'     => 'Метки',
                ],
            ],

            'pricing' => [
                'title' => 'Pricing',

                'fields' => [
                    'price' => 'Цена',
                    'cost'  => 'Стоимость',
                ],
            ],

            'additional' => [
                'title' => 'Additional',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'        => 'Название',
            'images'      => 'Images',
            'type'        => 'Тип',
            'reference'   => 'Ссылка',
            'responsible' => 'Responsible',
            'barcode'     => 'Штрихкод',
            'category'    => 'Категория',
            'company'     => 'Компания',
            'price'       => 'Цена',
            'cost'        => 'Стоимость',
            'tags'        => 'Метки',
            'deleted-at'  => 'Удалено',
            'created-at'  => 'Создано',
            'updated-at'  => 'Обновлено',
        ],

        'groups' => [
            'type'       => 'Тип',
            'category'   => 'Категория',
            'created-at' => 'Создано',
        ],

        'filters' => [
            'name'        => 'Название',
            'type'        => 'Тип',
            'reference'   => 'Ссылка',
            'barcode'     => 'Штрихкод',
            'category'    => 'Категория',
            'company'     => 'Компания',
            'price'       => 'Цена',
            'cost'        => 'Стоимость',
            'is-favorite' => 'Favorite',
            'weight'      => 'Вес',
            'volume'      => 'Объём',
            'tags'        => 'Метки',
            'responsible' => 'Responsible',
            'created-at'  => 'Создано',
            'updated-at'  => 'Обновлено',
            'creator'     => 'Creator',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Товар restored',
                    'body'  => 'товар есть restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Товар удалено',
                    'body'  => 'товар есть удалено successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Товар force удалено',
                    'body'  => 'товар есть force удалено successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Товары restored',
                    'body'  => 'товары есть restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Товары удалено',
                    'body'  => 'товары есть удалено successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Товары force удалено',
                    'body'  => 'товары есть force удалено successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Общее',

                'entries' => [
                    'name'             => 'Название',
                    'name-placeholder' => 'eg. T-shirt',
                    'description'      => 'Описание',
                    'tags'             => 'Метки',
                ],
            ],

            'images' => [
                'title' => 'Images',

                'entries' => [],
            ],

            'settings' => [
                'title' => 'Настройки',

                'entries' => [
                    'type'      => 'Тип',
                    'reference' => 'Ссылка',
                    'barcode'   => 'Штрихкод',
                    'category'  => 'Категория',
                    'company'   => 'Компания',
                ],
            ],

            'pricing' => [
                'title' => 'Pricing',

                'entries' => [
                    'price' => 'Цена',
                    'cost'  => 'Стоимость',
                ],
            ],

            'inventory' => [
                'title' => 'Склад',

                'entries' => [],

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

            'record-information' => [
                'title' => 'Запись Сведения',

                'entries' => [
                    'created-at' => 'Создано',
                    'created-by' => 'Создано От',
                    'updated-at' => 'Обновлено',
                ],
            ],
        ],
    ],
];
