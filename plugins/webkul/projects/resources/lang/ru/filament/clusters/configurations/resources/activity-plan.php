<?php

return [
    'navigation' => [
        'title' => 'Активность Plans',
    ],

    'form' => [
        'name'   => 'Название',
        'status' => 'Статус',
    ],

    'table' => [
        'columns' => [
            'name'       => 'Название',
            'status'     => 'Статус',
            'created-at' => 'Создано',
            'updated-at' => 'Обновлено',
        ],

        'groups' => [
            'name'       => 'Название',
            'status'     => 'Статус',
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
        'name'   => 'Название',
        'status' => 'Статус',
    ],
];
