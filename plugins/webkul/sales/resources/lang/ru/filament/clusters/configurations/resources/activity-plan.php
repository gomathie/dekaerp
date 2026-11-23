<?php

return [
    'navigation' => [
        'title' => 'Активность Plans',
        'group' => 'Деятельность',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Общее Сведения',
                'fields' => [
                    'name'       => 'Название',
                    'status'     => 'Статус',
                    'department' => 'Отдел',
                    'company'    => 'Компания',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'       => 'Название',
            'status'     => 'Статус',
            'department' => 'Отдел',
            'company'    => 'Компания',
            'manager'    => 'Руководитель',
            'created-by' => 'Создано От',
            'created-at' => 'Создано',
            'updated-at' => 'Обновлено',
        ],

        'filters' => [
            'name'           => 'Название',
            'plugin'         => 'Модуль',
            'activity-types' => 'Активность Types',
            'company'        => 'Компания',
            'department'     => 'Отдел',
            'is-active'      => 'Статус',
            'updated-at'     => 'Обновлено',
            'created-at'     => 'Создано',
        ],

        'groups' => [
            'status'     => 'Статус',
            'name'       => 'Название',
            'created-by' => 'Создано От',
            'created-at' => 'Создано',
            'updated-at' => 'Обновлено',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Активность Plan restored',
                    'body'  => 'активность plan есть restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Активность Plan удалено',
                    'body'  => 'активность plan есть удалено successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Активность Plan force удалено',
                    'body'  => 'активность plan есть force удалено successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Активность Plans restored',
                    'body'  => 'активность plans есть restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Активность Plans удалено',
                    'body'  => 'активность plans есть удалено successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Активность Plans force удалено',
                    'body'  => 'активность plans есть force удалено successfully.',
                ],
            ],
        ],

        'empty-state' => [
            'create' => [
                'notification' => [
                    'title' => 'Активность Plan создано',
                    'body'  => 'активность plan есть создано successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'   => 'Общее Сведения',
                'entries' => [
                    'name'       => 'Название',
                    'status'     => 'Статус',
                    'department' => 'Отдел',
                    'manager'    => 'Руководитель',
                    'company'    => 'Компания',
                ],
            ],
        ],
    ],
];
