<?php

return [
    'navigation' => [
        'title' => 'Маршруты',
        'group' => 'Склад Management',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Общее',

                'fields' => [
                    'route'             => 'Маршрут',
                    'route-placeholder' => 'eg. Two Step Reception',
                    'company'           => 'Компания',
                ],
            ],

            'applicable-on' => [
                'title'       => 'Applicable Вкл.',
                'description' => 'Выбрать места где этот маршрут можно быть применено.',

                'fields' => [
                    'products'                        => 'Товары',
                    'products-hint-tooltip'           => 'Если выбрано, этот маршрут быть доступно для selection вкл. товар.',
                    'product-categories'              => 'Товар Категории',
                    'product-categories-hint-tooltip' => 'Если выбрано, этот маршрут быть доступно для selection вкл. товар категория.',
                    'warehouses'                      => 'Склады',
                    'warehouses-hint-tooltip'         => 'Когда склад назначен до этот маршрут, это быть considered по умолчанию маршрут для товары moving через тот склад.',
                    'packaging'                       => 'Упаковка',
                    'packaging-hint-tooltip'          => 'Если выбрано, этот маршрут быть доступно для selection вкл. упаковка.',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'route'      => 'Маршрут',
            'company'    => 'Компания',
            'deleted-at' => 'Удалено',
            'created-at' => 'Создано',
            'updated-at' => 'Обновлено',
        ],

        'groups' => [
            'created-at' => 'Создано',
            'updated-at' => 'Обновлено',
        ],

        'filters' => [
            'company'  => 'Компания',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Маршрут обновлено',
                    'body'  => 'маршрут есть обновлено successfully.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Маршрут restored',
                    'body'  => 'маршрут есть restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Маршрут удалено',
                    'body'  => 'маршрут есть удалено successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Маршрут force удалено',
                        'body'  => 'маршрут есть force удалено successfully.',
                    ],

                    'error' => [
                        'title' => 'Маршрут could не быть удалено',
                        'body'  => 'маршрут нельзя быть удалено потому что это currently в использовать.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Маршруты restored',
                    'body'  => 'маршруты есть restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Маршруты удалено',
                    'body'  => 'маршруты есть удалено successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Маршруты force удалено',
                        'body'  => 'маршруты есть force удалено successfully.',
                    ],

                    'error' => [
                        'title' => 'Маршруты could не быть удалено',
                        'body'  => 'маршруты нельзя быть удалено потому что они currently в использовать.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'  => 'Общее',

                'entries' => [
                    'route'             => 'Маршрут',
                    'route-placeholder' => 'eg. Two Step Reception',
                    'company'           => 'Компания',
                ],
            ],

            'applicable-on' => [
                'title'       => 'Applicable Вкл.',
                'description' => 'Выбрать places где этот маршрут можно быть выбрано.',

                'entries' => [
                    'products'                        => 'Товары',
                    'products-hint-tooltip'           => 'Если выбрано, этот маршрут быть доступно для selection вкл. товар.',
                    'product-categories'              => 'Товар Категории',
                    'product-categories-hint-tooltip' => 'Если выбрано, этот маршрут быть доступно для selection вкл. товар категория.',
                    'warehouses'                      => 'Склады',
                    'warehouses-hint-tooltip'         => 'Когда склад назначен до этот маршрут, это быть considered по умолчанию маршрут для товары moving через тот склад.',
                    'packaging'                       => 'Упаковка',
                    'packaging-hint-tooltip'          => 'Если выбрано, этот маршрут быть доступно для selection вкл. упаковка.',
                ],
            ],

            'record-information' => [
                'title' => 'Запись Сведения',

                'entries' => [
                    'created-by'   => 'Создано От',
                    'created-at'   => 'Создано',
                    'last-updated' => 'Последний Обновлено',
                ],
            ],
        ],
    ],
];
