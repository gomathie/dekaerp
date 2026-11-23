<?php

return [
    'navigation' => [
        'title' => 'Replenishment',
        'group' => 'Procurement',
    ],

    'form' => [
        'fields' => [
        ],
    ],

    'table' => [
        'columns' => [
            'product'           => 'Товар',
            'location'          => 'Место',
            'route'             => 'Маршрут',
            'vendor'            => 'Поставщик',
            'trigger'           => 'Trigger',
            'on-hand'           => 'Вкл. Вручную',
            'min'               => 'Min',
            'max'               => 'Max',
            'multiple-quantity' => 'Multiple Количество',
            'to-order'          => 'До Заказ',
            'uom'               => 'UOM',
            'company'           => 'Компания',
        ],

        'groups' => [
            'location' => 'Место',
            'product'  => 'Товар',
            'category' => 'Категория',
        ],

        'filters' => [
        ],

        'header-actions' => [
            'create' => [
                'label' => 'Добавить Replenishment',

                'notification' => [
                    'title' => 'Replenishment added',
                    'body'  => 'replenishment есть added successfully.',
                ],

                'before' => [
                    'notification' => [
                        'title' => 'Replenishment уже существует',
                        'body'  => 'replenishment уже существует для этот конфигурация. Пожалуйста обновить existing replenishment instead.',
                    ],
                ],
            ],
        ],

        'actions' => [
        ],
    ],
];
