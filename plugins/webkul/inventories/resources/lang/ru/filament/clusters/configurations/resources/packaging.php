<?php

return [
    'navigation' => [
        'title' => 'Packagings',
        'group' => 'Товары',
    ],

    'form' => [
        'package-type' => 'Упаковка Тип',
        'routes'       => 'Маршруты',
    ],

    'table' => [
        'columns' => [
            'package-type' => 'Упаковка Тип',
        ],

        'groups' => [
            'package-type' => 'Упаковка Тип',
        ],

        'filters' => [
            'package-type' => 'Упаковка Тип',
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'entries' => [
                    'package_type' => 'Упаковка Тип',
                ],
            ],

            'routing' => [
                'title' => 'Routing Сведения',

                'entries' => [
                    'routes'     => 'Склад Маршруты',
                    'route_name' => 'Маршрут Название',
                ],
            ],
        ],
    ],
];
