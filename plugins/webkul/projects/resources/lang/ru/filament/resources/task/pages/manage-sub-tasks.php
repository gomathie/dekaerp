<?php

return [
    'title' => 'Подзадачи',

    'table' => [
        'header-actions' => [
            'create' => [
                'label' => 'Добавить подзадачу',

                'notification' => [
                    'title' => 'Задача создана',
                    'body'  => 'Задача успешно создана.',
                ],
            ],
        ],

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
];
