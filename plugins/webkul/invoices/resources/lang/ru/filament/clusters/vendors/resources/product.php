<?php

return [
    'navigation' => [
        'title' => 'Товары',
        'group' => 'Запасы',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Основное',

                'fields' => [
                    'name'             => 'Название',
                    'name-placeholder' => 'напр. Футболка',
                    'description'      => 'Описание',
                    'tags'             => 'Теги',
                    'sales'            => 'Продажи',
                    'purchase'         => 'Закупки',
                ],
            ],

            'invoice-policy' => [
                'title'            => 'Политика выставления счетов',
                'ordered-policy'   => 'Счёт можно выставить до отгрузки товара.',
                'delivered-policy' => 'Счёт после отгрузки, по отгруженному, а не заказанному количеству.',
            ],

            'images' => [
                'title' => 'Изображения',
            ],

            'settings' => [
                'title' => 'Настройки',

                'fields' => [
                    'type'      => 'Тип',
                    'reference' => 'Артикул',
                    'barcode'   => 'Штрихкод',
                    'category'  => 'Категория',
                    'company'   => 'Компания',
                ],
            ],

            'category-and-tags' => [
                'title' => 'Категория и теги',

                'fields' => [
                    'category' => 'Категория',
                    'tags'     => 'Теги',
                ],
            ],

            'pricing' => [
                'title' => 'Цены',

                'fields' => [
                    'price' => 'Цена',
                    'cost'  => 'Себестоимость',
                ],
            ],

            'additional' => [
                'title' => 'Дополнительно',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'        => 'Название',
            'images'      => 'Изображения',
            'type'        => 'Тип',
            'reference'   => 'Артикул',
            'responsible' => 'Ответственный',
            'barcode'     => 'Штрихкод',
            'category'    => 'Категория',
            'company'     => 'Компания',
            'price'       => 'Цена',
            'cost'        => 'Себестоимость',
            'tags'        => 'Теги',
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
            'reference'   => 'Артикул',
            'barcode'     => 'Штрихкод',
            'category'    => 'Категория',
            'company'     => 'Компания',
            'price'       => 'Цена',
            'cost'        => 'Себестоимость',
            'is-favorite' => 'Избранное',
            'weight'      => 'Вес',
            'volume'      => 'Объём',
            'tags'        => 'Теги',
            'responsible' => 'Ответственный',
            'created-at'  => 'Создано',
            'updated-at'  => 'Обновлено',
            'creator'     => 'Создатель',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Товар восстановлен',
                    'body'  => 'Товар успешно восстановлен.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Товар удалён',
                    'body'  => 'Товар успешно удалён.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Товар удалён навсегда',
                    'body'  => 'Товар успешно удалён навсегда.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Товары восстановлены',
                    'body'  => 'Товары успешно восстановлены.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Товары удалены',
                    'body'  => 'Товары успешно удалены.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Товары удалены навсегда',
                    'body'  => 'Товары успешно удалены навсегда.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Основное',

                'entries' => [
                    'name'             => 'Название',
                    'name-placeholder' => 'напр. Футболка',
                    'description'      => 'Описание',
                    'tags'             => 'Теги',
                ],
            ],

            'images' => [
                'title' => 'Изображения',

                'entries' => [],
            ],

            'settings' => [
                'title' => 'Настройки',

                'entries' => [
                    'type'      => 'Тип',
                    'reference' => 'Артикул',
                    'barcode'   => 'Штрихкод',
                    'category'  => 'Категория',
                    'company'   => 'Компания',
                ],
            ],

            'pricing' => [
                'title' => 'Цены',

                'entries' => [
                    'price' => 'Цена',
                    'cost'  => 'Себестоимость',
                ],
            ],

            'inventory' => [
                'title' => 'Запасы',

                'entries' => [],

                'fieldsets' => [
                    'tracking' => [
                        'title' => 'Отслеживание',

                        'entries' => [
                            'track-inventory' => 'Учитывать запасы',
                            'track-by'        => 'Учёт по',
                            'expiration-date' => 'Срок годности',
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
                            'responsible' => 'Ответственный',
                            'weight'      => 'Вес',
                            'volume'      => 'Объём',
                            'sale-delay'  => 'Срок поставки клиенту (дни)',
                        ],
                    ],

                    'traceability' => [
                        'title' => 'Прослеживаемость',

                        'entries' => [
                            'expiration-date'  => 'Срок годности (дни)',
                            'best-before-date' => 'Годен до (дни)',
                            'removal-date'     => 'Дата изъятия (дни)',
                            'alert-date'       => 'Дата оповещения (дни)',
                        ],
                    ],
                ],
            ],

            'record-information' => [
                'title' => 'Сведения о записи',

                'entries' => [
                    'created-at' => 'Создано',
                    'created-by' => 'Кем создано',
                    'updated-at' => 'Обновлено',
                ],
            ],
        ],
    ],
];
