<?php

return [
    'navigation' => [
        'title' => 'Категории',
        'group' => 'Товары',
    ],

    'form' => [
        'sections' => [
            'inventory' => [
                'title' => 'Запасы',

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
                'title' => 'Запасы',

                'subsections' => [
                    'logistics' => [
                        'title' => 'Логистика',

                        'entries' => [
                            'routes'     => 'Маршруты склада',
                            'route_name' => 'Название маршрута',
                        ],
                    ],
                ],
            ],
        ],
    ],
];
