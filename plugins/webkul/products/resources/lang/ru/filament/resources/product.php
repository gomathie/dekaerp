<?php

return [
    'global-search' => [
        'reference' => 'Ссылка',
        'barcode'   => 'Штрихкод',
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
                ],
            ],

            'images' => [
                'title' => 'Images',
            ],

            'inventory' => [
                'title' => 'Склад',

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
                    'reference'           => 'Ссылка',
                    'barcode'             => 'Штрихкод',
                    'category'            => 'Категория',
                    'company'             => 'Компания',
                    'company-placeholder' => 'Все Компании',
                ],
            ],

            'pricing' => [
                'title' => 'Pricing',

                'fields' => [
                    'price'           => 'Цена',
                    'cost'            => 'Стоимость',
                    'uom-placeholder' => 'UOM',
                ],
            ],

            'additional' => [
                'title' => 'Additional',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'favorite'            => 'Favorite',
            'name'                => 'Название',
            'variants'            => 'Variants',
            'images'              => 'Images',
            'type'                => 'Тип',
            'reference'           => 'Ссылка',
            'responsible'         => 'Responsible',
            'barcode'             => 'Штрихкод',
            'category'            => 'Категория',
            'company'             => 'Компания',
            'company-placeholder' => 'Все Компании',
            'price'               => 'Цена',
            'cost'                => 'Стоимость',
            'on-hand'             => 'Вкл. Вручную',
            'tags'                => 'Метки',
            'deleted-at'          => 'Удалено',
            'created-at'          => 'Создано',
            'updated-at'          => 'Обновлено',
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
                    'success' => [
                        'title' => 'Товар force удалено',
                        'body'  => 'товар есть force удалено successfully.',
                    ],

                    'error' => [
                        'title' => 'Товар could не быть удалено',
                        'body'  => 'товар нельзя быть удалено потому что это currently в использовать.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'print' => [
                'label' => 'Печать Labels',

                'form' => [
                    'fields' => [
                        'quantity' => 'Номер Labels',
                        'format'   => 'Формат',

                        'format-options' => [
                            'dymo'       => 'Dymo',
                            '2x7_price'  => '2x7 с цена',
                            '4x7_price'  => '4x7 с цена',
                            '4x12'       => '4x12',
                            '4x12_price' => '4x12 с цена',
                        ],
                    ],
                ],
            ],

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
                    'success' => [
                        'title' => 'Товары force удалено',
                        'body'  => 'товары есть force удалено successfully.',
                    ],

                    'error' => [
                        'title' => 'Товары could не быть удалено',
                        'body'  => 'товары нельзя быть удалено потому что они currently в использовать.',
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
