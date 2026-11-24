<?php

return [
    'navigation' => [
        'title' => 'Фасовки',
        'group' => 'Товары',
    ],

    'form' => [
        'package-type' => 'Тип упаковки',
        'routes'       => 'Маршруты',
    ],

    'table' => [
        'columns' => [
            'package-type' => 'Тип упаковки',
        ],

        'groups' => [
            'package-type' => 'Тип упаковки',
        ],

        'filters' => [
            'package-type' => 'Тип упаковки',
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'entries' => [
                    'package_type' => 'Тип упаковки',
                ],
            ],

            'routing' => [
                'title' => 'Маршрутизация',

                'entries' => [
                    'routes'     => 'Маршруты склада',
                    'route_name' => 'Название маршрута',
                ],
            ],
        ],
    ],
];
