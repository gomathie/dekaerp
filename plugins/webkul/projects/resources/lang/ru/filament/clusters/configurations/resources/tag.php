<?php

return [
    'navigation' => [
        'title' => 'Метки',
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
                    'title' => 'Метка обновлено',
                    'body'  => 'метка есть обновлено successfully.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Метка restored',
                    'body'  => 'метка есть restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Метка удалено',
                    'body'  => 'метка есть удалено successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Метка force удалено',
                    'body'  => 'метка есть force удалено successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Метки restored',
                    'body'  => 'метки есть restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Метки удалено',
                    'body'  => 'метки есть удалено successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Метки force удалено',
                    'body'  => 'метки есть force удалено successfully.',
                ],
            ],
        ],
    ],
];
