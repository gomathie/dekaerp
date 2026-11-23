<?php

return [
    'navigation' => [
        'title' => 'Storage Категории',
        'group' => 'Склад Management',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Общее',

                'fields' => [
                    'name'               => 'Название',
                    'allow-new-products' => 'Разрешить Новое Товары',
                    'max-weight'         => 'Max Вес',
                    'company'            => 'Компания',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'               => 'Название',
            'allow-new-products' => 'Разрешить Новое Товары',
            'max-weight'         => 'Max Вес',
            'company'            => 'Компания',
            'deleted-at'         => 'Удалено',
            'created-at'         => 'Создано',
            'updated-at'         => 'Обновлено',
        ],

        'groups' => [
            'allow-new-products' => 'Разрешить Новое Товары',
            'created-at'         => 'Создано',
            'updated-at'         => 'Обновлено',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Storage Категория удалено',
                    'body'  => 'storage категория есть удалено successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Storage Категории удалено',
                    'body'  => 'storage категории есть удалено successfully.',
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
                    'allow-new-products' => 'Разрешить Новое Товары',
                    'max-weight'         => 'Max Вес',
                    'company'            => 'Компания',
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
