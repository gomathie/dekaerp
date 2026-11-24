<?php

return [
    'navigation' => [
        'title' => 'Пополнение',
        'group' => 'Снабжение',
    ],

    'form' => [
        'fields' => [
        ],
    ],

    'table' => [
        'columns' => [
            'product'           => 'Товар',
            'location'          => 'Местоположение',
            'route'             => 'Маршрут',
            'vendor'            => 'Поставщик',
            'trigger'           => 'Триггер',
            'on-hand'           => 'В наличии',
            'min'               => 'Мин.',
            'max'               => 'Макс.',
            'multiple-quantity' => 'Кратность',
            'to-order'          => 'К заказу',
            'uom'               => 'Ед. изм.',
            'company'           => 'Компания',
        ],

        'groups' => [
            'location' => 'Местоположение',
            'product'  => 'Товар',
            'category' => 'Категория',
        ],

        'filters' => [
        ],

        'header-actions' => [
            'create' => [
                'label' => 'Добавить пополнение',

                'notification' => [
                    'title' => 'Пополнение добавлено',
                    'body'  => 'Пополнение успешно добавлено.',
                ],

                'before' => [
                    'notification' => [
                        'title' => 'Пополнение уже существует',
                        'body'  => 'Для этой конфигурации пополнение уже существует. Обновите существующее пополнение.',
                    ],
                ],
            ],
        ],

        'actions' => [
        ],
    ],
];
