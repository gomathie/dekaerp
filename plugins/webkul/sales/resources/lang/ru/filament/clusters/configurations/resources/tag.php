<?php

return [
    'title' => 'Тег',

    'navigation' => [
        'title' => 'Тег',
        'group' => 'Заказы на продажу',
    ],

    'form' => [
        'fields' => [
            'name'  => 'Название',
            'color' => 'Цвет',
        ],
    ],

    'table' => [
        'columns' => [
            'created-by' => 'Кем создано',
            'name'       => 'Название',
            'color'      => 'Цвет',
        ],
        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Тег обновлён',
                    'body'  => 'Тег успешно обновлён.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Тег удалён',
                    'body'  => 'Тег успешно удалён.',
                ],
            ],
        ],
        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Тег удалён',
                    'body'  => 'Тег успешно удалён.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'name'  => 'Название',
            'color' => 'Цвет',
        ],
    ],
];
