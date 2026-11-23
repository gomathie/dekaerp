<?php

return [
    'navigation' => [
        'title' => 'Задача Этапы',
    ],

    'form' => [
        'name'    => 'Название',
        'project' => 'Проект',
    ],

    'table' => [
        'columns' => [
            'name'       => 'Название',
            'project'    => 'Проект',
            'created-at' => 'Создано',
            'updated-at' => 'Обновлено',
        ],

        'groups' => [
            'project'    => 'Проект',
            'created-at' => 'Создано',
        ],

        'filters' => [
            'project' => 'Проект',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Задача этап обновлено',
                    'body'  => 'задача этап есть обновлено successfully.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Задача этап restored',
                    'body'  => 'задача этап есть restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Задача этап удалено',
                    'body'  => 'задача этап есть удалено successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Задача этап force удалено',
                        'body'  => 'Задача этап есть force удалено successfully.',
                    ],
                    'error' => [
                        'title' => 'Задача этап could не быть удалено',
                        'body'  => 'Задача этап нельзя быть удалено потому что это currently в использовать.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Задача этапы restored',
                    'body'  => 'задача этапы есть restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Задача этапы удалено',
                    'body'  => 'задача этапы есть удалено successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Задача этапы force удалено',
                    'body'  => 'задача этапы есть force удалено successfully.',
                ],
            ],
        ],
    ],
];
