<?php

return [
    'navigation' => [
        'title' => 'Milestones',
    ],

    'form' => [
        'name'         => 'Название',
        'deadline'     => 'Deadline',
        'is-completed' => 'Завершено',
        'project'      => 'Проект',
    ],

    'table' => [
        'columns' => [
            'name'         => 'Название',
            'deadline'     => 'Deadline',
            'is-completed' => 'Завершено',
            'completed-at' => 'Завершено В',
            'project'      => 'Проект',
            'creator'      => 'Creator',
            'created-at'   => 'Создано',
            'updated-at'   => 'Обновлено',
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
                    'title' => 'Веха обновить',
                    'body'  => 'веха есть обновить successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Веха удалено',
                    'body'  => 'веха есть удалено successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Milestones удалено',
                    'body'  => 'milestones есть удалено successfully.',
                ],
            ],
        ],
    ],
];
