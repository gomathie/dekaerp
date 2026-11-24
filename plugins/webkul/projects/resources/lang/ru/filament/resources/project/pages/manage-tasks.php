<?php

return [
    'title' => 'Задачи',

    'header-actions' => [
        'create' => [
            'label' => 'Новая задача',
        ],
    ],

    'table' => [
        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Задача восстановлена',
                    'body'  => 'Задача успешно восстановлена.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Задача удалена',
                    'body'  => 'Задача успешно удалена.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Задача удалена безвозвратно',
                    'body'  => 'Задача удалена безвозвратно.',
                ],
            ],
        ],
    ],

    'tabs' => [
        'open-tasks'       => 'Открытые задачи',
        'my-tasks'         => 'Мои задачи',
        'unassigned-tasks' => 'Неназначенные задачи',
        'closed-tasks'     => 'Закрытые задачи',
        'starred-tasks'    => 'Отмеченные задачи',
        'archived-tasks'   => 'Задачи в архиве',
    ],
];
