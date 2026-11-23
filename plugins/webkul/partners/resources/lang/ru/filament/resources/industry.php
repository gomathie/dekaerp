<?php

return [
    'navigation' => [
        'title' => 'Industries',
    ],

    'form' => [
        'name'      => 'Название',
        'full-name' => 'Полный Название',
    ],

    'table' => [
        'columns' => [
            'name'       => 'Название',
            'full-name'  => 'Полный Название',
            'created-at' => 'Создано',
            'updated-at' => 'Обновлено',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Industry обновлено',
                    'body'  => 'industry есть обновлено successfully.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Industry restored',
                    'body'  => 'industry есть restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Industry удалено',
                    'body'  => 'industry есть удалено successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Industry force удалено',
                    'body'  => 'industry есть force удалено successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Industries restored',
                    'body'  => 'industries есть restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Industries удалено',
                    'body'  => 'industries есть удалено successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Industries force удалено',
                    'body'  => 'industries есть force удалено successfully.',
                ],
            ],
        ],
    ],
];
