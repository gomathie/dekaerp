<?php

return [
    'navigation' => [
        'title' => 'Kategoria',
        'group' => 'Bidhaa',
    ],

    'form' => [
        'sections' => [
            'inventory' => [
                'title' => 'Bidhaa ghalani',

                'fieldsets' => [
                    'logistics' => [
                        'title' => 'Lojistiki',

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
                'title' => 'Bidhaa ghalani',

                'subsections' => [
                    'logistics' => [
                        'title' => 'Lojistiki',

                        'entries' => [
                            'routes'     => 'Njia za ghala',
                            'route_name' => 'Jina la njia',
                        ],
                    ],
                ],
            ],
        ],
    ],
];
