<?php

return [
    'navigation' => [
        'title' => 'Quantities',
        'group' => 'Adjustments',
    ],

    'form' => [
        'fields' => [
            'location'         => 'Место',
            'product'          => 'Товар',
            'package'          => 'Упаковка',
            'lot'              => 'Партия / Серийный номер Numbers',
            'counted-qty'      => 'Counted Количество',
            'scheduled-at'     => 'Scheduled В',
            'storage-category' => 'Storage Категория',
        ],
    ],

    'table' => [
        'columns' => [
            'location'           => 'Место',
            'product'            => 'Товар',
            'product-category'   => 'Товар Категория',
            'lot'                => 'Партия / Серийный номер Numbers',
            'storage-category'   => 'Storage Категория',
            'available-quantity' => 'Доступно Количество',
            'quantity'           => 'Количество',
            'package'            => 'Упаковка',
            'last-counted-at'    => 'Последний Counted В',
            'on-hand'            => 'Вкл. Вручную Количество',
            'uom'                => 'UOM',
            'counted'            => 'Counted Количество',
            'difference'         => 'Difference',
            'scheduled-at'       => 'Scheduled В',
            'user'               => 'Пользователь',
            'company'            => 'Компания',

            'on-hand-before-state-updated' => [
                'notification' => [
                    'title' => 'Количество обновлено',
                    'body'  => 'количество есть обновлено successfully.',
                ],
            ],
        ],

        'groups' => [
            'product'          => 'Товар',
            'product-category' => 'Товар Категория',
            'location'         => 'Место',
            'storage-category' => 'Storage Категория',
            'lot'              => 'Партия / Серийный номер Numbers',
            'company'          => 'Компания',
            'package'          => 'Упаковка',
        ],

        'filters' => [
            'product'             => 'Товар',
            'uom'                 => 'Единица измерения',
            'product-category'    => 'Товар Категория',
            'location'            => 'Место',
            'storage-category'    => 'Storage Категория',
            'lot'                 => 'Партия / Серийный номер Numbers',
            'company'             => 'Компания',
            'package'             => 'Упаковка',
            'on-hand-quantity'    => 'Вкл. Вручную Количество',
            'difference-quantity' => 'Difference Количество',
            'incoming-at'         => 'Incoming В',
            'scheduled-at'        => 'Scheduled В',
            'user'                => 'Пользователь',
            'created-at'          => 'Создано',
            'updated-at'          => 'Обновлено',
            'company'             => 'Компания',
            'creator'             => 'Creator',
        ],

        'header-actions' => [
            'create' => [
                'label' => 'Добавить Количество',

                'notification' => [
                    'title' => 'Количество added',
                    'body'  => 'количество есть added successfully.',
                ],

                'before' => [
                    'notification' => [
                        'title' => 'Количество уже существует',
                        'body'  => 'количество уже существует для этот конфигурация. Пожалуйста обновить existing количество instead.',
                    ],
                ],
            ],
        ],

        'actions' => [
            'apply' => [
                'label' => 'Применить',

                'notification' => [
                    'title' => 'Количество changes применено',
                    'body'  => 'количество changes есть применено successfully.',
                ],
            ],

            'clear' => [
                'label' => 'Очистить',

                'notification' => [
                    'title' => 'Количество changes cleared',
                    'body'  => 'количество changes есть cleared successfully.',
                ],
            ],
        ],
    ],
];
