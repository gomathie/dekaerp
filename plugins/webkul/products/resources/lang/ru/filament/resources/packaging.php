<?php

return [
    'form' => [
        'name'    => 'Название',
        'barcode' => 'Штрихкод',
        'product' => 'Товар',
        'routes'  => 'Маршруты',
        'qty'     => 'Кол-во',
        'company' => 'Компания',
    ],

    'table' => [
        'columns' => [
            'name'       => 'Название',
            'product'    => 'Товар',
            'routes'     => 'Маршруты',
            'qty'        => 'Кол-во',
            'company'    => 'Компания',
            'barcode'    => 'Штрихкод',
            'created-at' => 'Создана',
            'updated-at' => 'Обновлена',
        ],

        'groups' => [
            'product'    => 'Товар',
            'created-at' => 'Создана',
            'updated-at' => 'Обновлена',
        ],

        'filters' => [
            'product' => 'Товар',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Упаковка обновлена',
                    'body'  => 'Упаковка успешно обновлена.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Упаковка удалена',
                        'body'  => 'Упаковка успешно удалена.',
                    ],

                    'error' => [
                        'title' => 'Не удалось удалить упаковку',
                        'body'  => 'Упаковку нельзя удалить, так как она используется.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'print' => [
                'label' => 'Печать',
            ],

            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Упаковки удалены',
                        'body'  => 'Упаковки успешно удалены.',
                    ],

                    'error' => [
                        'title' => 'Не удалось удалить упаковки',
                        'body'  => 'Упаковки нельзя удалить, так как они используются.',
                    ],
                ],
            ],
        ],

        'empty-state-actions' => [
            'create' => [
                'label' => 'Новая упаковка',

                'notification' => [
                    'title' => 'Упаковка создана',
                    'body'  => 'Упаковка успешно создана.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Общая информация',

                'entries' => [
                    'name'    => 'Название упаковки',
                    'barcode' => 'Штрихкод',
                    'product' => 'Товар',
                    'qty'     => 'Количество',
                ],
            ],

            'organization' => [
                'title' => 'Сведения об организации',

                'entries' => [
                    'company'    => 'Компания',
                    'creator'    => 'Автор',
                    'created_at' => 'Создана',
                    'updated_at' => 'Последнее обновление',
                ],
            ],
        ],
    ],
];
