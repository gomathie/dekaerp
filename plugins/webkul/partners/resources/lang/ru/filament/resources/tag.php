<?php

return [
    'navigation' => [
        'title' => 'Теги',
    ],

    'form' => [
        'name'  => 'Название',
        'color' => 'Цвет',
    ],

    'table' => [
        'columns' => [
            'name'       => 'Название',
            'color'      => 'Цвет',
            'created-at' => 'Создано',
            'updated-at' => 'Обновлено',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Тег обновлён',
                    'body'  => 'Тег успешно обновлён.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Тег восстановлен',
                    'body'  => 'Тег успешно восстановлен.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Тег удалён',
                    'body'  => 'Тег успешно удалён.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Тег удалён навсегда',
                    'body'  => 'Тег успешно удалён навсегда.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Теги восстановлены',
                    'body'  => 'Теги успешно восстановлены.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Теги удалены',
                    'body'  => 'Теги успешно удалены.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Теги удалены навсегда',
                    'body'  => 'Теги успешно удалены навсегда.',
                ],
            ],
        ],
    ],
];
