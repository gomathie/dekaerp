<?php

return [
    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Общее',

                'fields' => [
                    'name'             => 'Название',
                    'name-placeholder' => 'напр. Лампы',
                    'parent'           => 'Родительская категория',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'        => 'Название',
            'full-name'   => 'Полное название',
            'parent-path' => 'Путь',
            'parent'      => 'Родительская категория',
            'creator'     => 'Автор',
            'created-at'  => 'Создана',
            'created-at'  => 'Создана',
            'updated-at'  => 'Обновлена',
        ],

        'groups' => [
            'parent'     => 'Родительская категория',
            'creator'    => 'Автор',
            'created-at' => 'Создана',
            'updated-at' => 'Обновлена',
        ],

        'filters' => [
            'parent'  => 'Родительская категория',
            'creator' => 'Автор',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Категория удалена',
                        'body'  => 'Категория успешно удалена.',
                    ],

                    'error' => [
                        'title' => 'Не удалось удалить категорию',
                        'body'  => 'Категорию нельзя удалить, так как она используется.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Категории удалены',
                        'body'  => 'Категории успешно удалены.',
                    ],

                    'error' => [
                        'title' => 'Не удалось удалить категории',
                        'body'  => 'Категории нельзя удалить, так как они используются.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Общая информация',

                'entries' => [
                    'name'        => 'Название',
                    'parent'      => 'Родительская категория',
                    'full_name'   => 'Полное название категории',
                    'parent_path' => 'Путь категории',
                ],
            ],

            'record-information' => [
                'title' => 'Сведения о записи',

                'entries' => [
                    'creator'    => 'Автор',
                    'created_at' => 'Создана',
                    'updated_at' => 'Последнее обновление',
                ],
            ],
        ],
    ],
];
