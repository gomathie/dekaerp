<?php

return [
    'navigation' => [
        'title' => 'Маршруты',
        'group' => 'Управление складом',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Общее',

                'fields' => [
                    'route'             => 'Маршрут',
                    'route-placeholder' => 'напр. Приёмка в два этапа',
                    'company'           => 'Компания',
                ],
            ],

            'applicable-on' => [
                'title'       => 'Применимо к',
                'description' => 'Выберите местоположения, где можно применять этот маршрут.',

                'fields' => [
                    'products'                        => 'Товары',
                    'products-hint-tooltip'           => 'Если отмечено, маршрут можно выбрать в карточке товара.',
                    'product-categories'              => 'Категории товаров',
                    'product-categories-hint-tooltip' => 'Если отмечено, маршрут можно выбрать в категории товаров.',
                    'warehouses'                      => 'Склады',
                    'warehouses-hint-tooltip'         => 'Если маршрут назначен складу, он считается маршрутом по умолчанию для товаров, проходящих через этот склад.',
                    'packaging'                       => 'Фасовка',
                    'packaging-hint-tooltip'          => 'Если отмечено, маршрут можно выбрать в фасовке.',
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
                    'title' => 'Маршрут обновлён',
                    'body'  => 'Маршрут успешно обновлён.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Маршрут восстановлен',
                    'body'  => 'Маршрут успешно восстановлен.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Маршрут удалён',
                    'body'  => 'Маршрут успешно удалён.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Маршрут удалён безвозвратно',
                        'body'  => 'Маршрут успешно удалён безвозвратно.',
                    ],

                    'error' => [
                        'title' => 'Не удалось удалить маршрут',
                        'body'  => 'Маршрут нельзя удалить, так как он используется.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Маршруты восстановлены',
                    'body'  => 'Маршруты успешно восстановлены.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Маршруты удалены',
                    'body'  => 'Маршруты успешно удалены.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Маршруты удалены безвозвратно',
                        'body'  => 'Маршруты успешно удалены безвозвратно.',
                    ],

                    'error' => [
                        'title' => 'Не удалось удалить маршруты',
                        'body'  => 'Маршруты нельзя удалить, так как они используются.',
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
                    'route-placeholder' => 'напр. Приёмка в два этапа',
                    'company'           => 'Компания',
                ],
            ],

            'applicable-on' => [
                'title'       => 'Применимо к',
                'description' => 'Выберите, где можно выбрать этот маршрут.',

                'entries' => [
                    'products'                        => 'Товары',
                    'products-hint-tooltip'           => 'Если отмечено, маршрут можно выбрать в карточке товара.',
                    'product-categories'              => 'Категории товаров',
                    'product-categories-hint-tooltip' => 'Если отмечено, маршрут можно выбрать в категории товаров.',
                    'warehouses'                      => 'Склады',
                    'warehouses-hint-tooltip'         => 'Если маршрут назначен складу, он считается маршрутом по умолчанию для товаров, проходящих через этот склад.',
                    'packaging'                       => 'Фасовка',
                    'packaging-hint-tooltip'          => 'Если отмечено, маршрут можно выбрать в фасовке.',
                ],
            ],

            'record-information' => [
                'title' => 'Сведения о записи',

                'entries' => [
                    'created-by'   => 'Создал',
                    'created-at'   => 'Создано',
                    'last-updated' => 'Последнее обновление',
                ],
            ],
        ],
    ],
];
