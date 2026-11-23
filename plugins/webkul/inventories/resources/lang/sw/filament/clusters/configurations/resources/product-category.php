<?php

return [
    'navigation' => [
        'title' => 'Kategoria',
        'group' => 'Bidhaa',
    ],

    'form' => [
        'sections' => [
            'inventory' => [
                'title' => 'Ghala',

                'fieldsets' => [
                    'logistics' => [
                        'title' => 'Usafirishaji',

                        'fields' => [
                            'routes' => 'Njia',
                        ],
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'inventory' => [
                'title' => 'Ghala',

                'subsections' => [
                    'logistics' => [
                        'title' => 'Usafirishaji',

                        'entries' => [
                            'routes'     => 'Ghala Njia',
                            'route_name' => 'Njia Jina',
                        ],
                    ],
                ],
            ],
        ],
    ],
];
