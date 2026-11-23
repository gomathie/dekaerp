<?php

return [
    'table' => [
        'header-actions' => [
            'create' => [
                'label' => 'Добавить Sub Задача',

                'notification' => [
                    'title' => 'Задача создано',
                    'body'  => 'задача есть создано successfully.',
                ],
            ],
        ],

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
];
