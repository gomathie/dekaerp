<?php

return [
    'navigation' => [
        'title' => 'Проект Этапы',
    ],

    'form' => [
        'name' => 'Название',
    ],

    'table' => [
        'columns' => [
            'name'       => 'Название',
            'created-at' => 'Создано',
            'updated-at' => 'Обновлено',
        ],

        'groups' => [
            'name'         => 'Название',
            'is-completed' => 'Завершено',
            'project'      => 'Проект',
            'created-at'   => 'Создано',
        ],

        'filters' => [
            'is-completed' => 'Завершено',
            'project'      => 'Проект',
            'creator'      => 'Creator',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Проект этап обновлено',
                    'body'  => 'проект этап есть обновлено successfully.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Проект этап restored',
                    'body'  => 'проект этап есть restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Проект этап удалено',
                    'body'  => 'проект этап есть удалено successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Проект этап force удалено',
                        'body'  => 'проект этап есть force удалено successfully.',
                    ],
                    'error' => [
                        'title' => 'Проект Этап could не быть удалено',
                        'body'  => 'Проект Этап нельзя быть удалено потому что это currently в использовать.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Проект этапы restored',
                    'body'  => 'проект этапы есть restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Проект этапы удалено',
                    'body'  => 'проект этапы есть удалено successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Проект этапы force удалено',
                    'body'  => 'проект этапы есть force удалено successfully.',
                ],
            ],
        ],
    ],
];
