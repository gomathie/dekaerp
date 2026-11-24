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
                    'name-placeholder' => 'напр. футболка',
                    'description'      => 'Описание',
                    'tags'             => 'Теги',
                    'sales'            => 'Продажи',
                    'purchase'         => 'Закупки',
                ],
            ],

            'invoice-policy' => [
                'title'            => 'Политика выставления счетов',
                'ordered-policy'   => 'Можно выставлять счёт до отгрузки товаров.',
                'delivered-policy' => 'Счёт выставляется после отгрузки по отгруженному, а не заказанному количеству.',
            ],

            'images' => [
                'title' => 'Изображения',
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
            'reference'   => 'Ссылка',
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
            'reference'   => 'Ссылка',
            'barcode'     => 'Штрихкод',
            'category'    => 'Категория',
            'company'     => 'Компания',
            'price'       => 'Цена',
            'cost'        => 'Себестоимость',
            'is-favorite' => 'В избранном',
            'weight'      => 'Вес',
            'volume'      => 'Объём',
            'tags'        => 'Теги',
            'responsible' => 'Ответственный',
            'created-at'  => 'Создано',
            'updated-at'  => 'Обновлено',
            'creator'     => 'Автор',
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
                    'name-placeholder' => 'напр. футболка',
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
                    'reference' => 'Ссылка',
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
                            'track-by'        => 'Отслеживать по',
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
                    'created-by' => 'Создал',
                    'updated-at' => 'Обновлено',
                ],
            ],
        ],
    ],
];
