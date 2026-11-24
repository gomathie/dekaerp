<?php

return [
    'global-search' => [
        'reference' => 'Артикул',
        'barcode'   => 'Штрихкод',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Общее',

                'fields' => [
                    'name'             => 'Название',
                    'name-placeholder' => 'напр. Футболка',
                    'description'      => 'Описание',
                    'tags'             => 'Теги',
                ],
            ],

            'images' => [
                'title' => 'Изображения',
            ],

            'inventory' => [
                'title' => 'Запасы',

                'fields' => [],

                'fieldsets' => [
                    'logistics' => [
                        'title' => 'Логистика',

                        'fields' => [
                            'weight' => 'Вес',
                            'volume' => 'Объём',
                        ],
                    ],
                ],
            ],

            'settings' => [
                'title' => 'Настройки',

                'fields' => [
                    'type'                => 'Тип',
                    'reference'           => 'Артикул',
                    'barcode'             => 'Штрихкод',
                    'category'            => 'Категория',
                    'company'             => 'Компания',
                    'company-placeholder' => 'Все компании',
                ],
            ],

            'pricing' => [
                'title' => 'Цены',

                'fields' => [
                    'price'           => 'Цена',
                    'cost'            => 'Себестоимость',
                    'uom-placeholder' => 'Ед. изм.',
                ],
            ],

            'additional' => [
                'title' => 'Дополнительно',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'favorite'            => 'Избранное',
            'name'                => 'Название',
            'variants'            => 'Варианты',
            'images'              => 'Изображения',
            'type'                => 'Тип',
            'reference'           => 'Артикул',
            'responsible'         => 'Ответственный',
            'barcode'             => 'Штрихкод',
            'category'            => 'Категория',
            'company'             => 'Компания',
            'company-placeholder' => 'Все компании',
            'price'               => 'Цена',
            'cost'                => 'Себестоимость',
            'on-hand'             => 'В наличии',
            'tags'                => 'Теги',
            'deleted-at'          => 'Удалён',
            'created-at'          => 'Создан',
            'updated-at'          => 'Обновлён',
        ],

        'groups' => [
            'type'       => 'Тип',
            'category'   => 'Категория',
            'created-at' => 'Создан',
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
            'is-favorite' => 'В избранном',
            'weight'      => 'Вес',
            'volume'      => 'Объём',
            'tags'        => 'Теги',
            'responsible' => 'Ответственный',
            'created-at'  => 'Создан',
            'updated-at'  => 'Обновлён',
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
                    'success' => [
                        'title' => 'Товар удалён навсегда',
                        'body'  => 'Товар успешно удалён навсегда.',
                    ],

                    'error' => [
                        'title' => 'Не удалось удалить товар',
                        'body'  => 'Товар нельзя удалить, так как он используется.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'print' => [
                'label' => 'Печать этикеток',

                'form' => [
                    'fields' => [
                        'quantity' => 'Количество этикеток',
                        'format'   => 'Формат',

                        'format-options' => [
                            'dymo'       => 'Dymo',
                            '2x7_price'  => '2x7 с ценой',
                            '4x7_price'  => '4x7 с ценой',
                            '4x12'       => '4x12',
                            '4x12_price' => '4x12 с ценой',
                        ],
                    ],
                ],
            ],

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
                    'success' => [
                        'title' => 'Товары удалены навсегда',
                        'body'  => 'Товары успешно удалены навсегда.',
                    ],

                    'error' => [
                        'title' => 'Не удалось удалить товары',
                        'body'  => 'Товары нельзя удалить, так как они используются.',
                    ],
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

                'fieldsets' => [
                    'logistics' => [
                        'title' => 'Логистика',

                        'entries' => [
                            'weight' => 'Вес',
                            'volume' => 'Объём',
                        ],
                    ],
                ],
            ],

            'record-information' => [
                'title' => 'Сведения о записи',

                'entries' => [
                    'created-at' => 'Создан',
                    'created-by' => 'Автор',
                    'updated-at' => 'Обновлён',
                ],
            ],
        ],
    ],
];
