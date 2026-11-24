<?php

return [
    'navigation' => [
        'title' => 'Вехи',
    ],

    'form' => [
        'name'         => 'Название',
        'deadline'     => 'Срок',
        'is-completed' => 'Завершено',
        'project'      => 'Проект',
    ],

    'table' => [
        'columns' => [
            'name'         => 'Название',
            'deadline'     => 'Срок',
            'is-completed' => 'Завершено',
            'completed-at' => 'Дата завершения',
            'project'      => 'Проект',
            'creator'      => 'Автор',
            'created-at'   => 'Дата создания',
            'updated-at'   => 'Дата изменения',
        ],

        'groups' => [
            'name'         => 'Название',
            'is-completed' => 'Завершено',
            'project'      => 'Проект',
            'created-at'   => 'Дата создания',
        ],

        'filters' => [
            'is-completed' => 'Завершено',
            'project'      => 'Проект',
            'creator'      => 'Автор',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Веха обновлена',
                    'body'  => 'Веха успешно обновлена.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Веха удалена',
                    'body'  => 'Веха успешно удалена.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Вехи удалены',
                    'body'  => 'Вехи успешно удалены.',
                ],
            ],
        ],
    ],
];
