<?php

return [
    'navigation' => [
        'title' => 'Категории',
        'group' => 'Товары',
    ],

    'form' => [
        'sections' => [
            'inventory' => [
                'title' => 'Склад',

                'fieldsets' => [
                    'logistics' => [
                        'title' => 'Логистика',

                        'fields' => [
                            'routes' => 'Маршруты',
                        ],
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'inventory' => [
                'title' => 'Склад',

                'subsections' => [
                    'logistics' => [
                        'title' => 'Логистика',

                        'entries' => [
                            'routes'     => 'Склад Маршруты',
                            'route_name' => 'Маршрут Название',
                        ],
                    ],
                ],
            ],
        ],
    ],
];
