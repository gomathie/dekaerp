<?php

return [
    'title' => 'Задачи',

    'header-actions' => [
        'create' => [
            'label' => 'Новое Задача',
        ],
    ],

    'table' => [
        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Задача restored',
                    'body'  => 'задача есть restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Задача удалено',
                    'body'  => 'задача есть удалено successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Задача force удалено',
                    'body'  => 'задача есть force удалено successfully.',
                ],
            ],
        ],
    ],

    'tabs' => [
        'open-tasks'       => 'Открыт Задачи',
        'my-tasks'         => 'Мой Задачи',
        'unassigned-tasks' => 'Не назначен Задачи',
        'closed-tasks'     => 'Закрыт Задачи',
        'starred-tasks'    => 'Starred Задачи',
        'archived-tasks'   => 'В архиве Задачи',
    ],
];
