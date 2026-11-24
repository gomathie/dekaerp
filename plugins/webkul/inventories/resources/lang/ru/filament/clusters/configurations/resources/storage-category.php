<?php

return [
    'navigation' => [
        'title' => 'Категории хранения',
        'group' => 'Управление складом',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Общее',

                'fields' => [
                    'name'               => 'Название',
                    'allow-new-products' => 'Разрешить новые товары',
                    'max-weight'         => 'Макс. вес',
                    'company'            => 'Компания',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'               => 'Название',
            'allow-new-products' => 'Разрешить новые товары',
            'max-weight'         => 'Макс. вес',
            'company'            => 'Компания',
            'deleted-at'         => 'Удалено',
            'created-at'         => 'Создано',
            'updated-at'         => 'Обновлено',
        ],

        'groups' => [
            'allow-new-products' => 'Разрешить новые товары',
            'created-at'         => 'Создано',
            'updated-at'         => 'Обновлено',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Категория хранения удалена',
                    'body'  => 'Категория хранения успешно удалена.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Категории хранения удалены',
                    'body'  => 'Категории хранения успешно удалены.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'  => 'Общее',

                'entries' => [
                    'name'               => 'Название',
                    'allow-new-products' => 'Разрешить новые товары',
                    'max-weight'         => 'Макс. вес',
                    'company'            => 'Компания',
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
