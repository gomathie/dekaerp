<?php

return [
    'title' => 'Табели',

    'navigation' => [
        'title' => 'Табели',
    ],

    'global-search' => [
        'project' => 'Проект',
        'task'    => 'Задача',
        'date'    => 'Дата',
    ],

    'form' => [
        'date'                   => 'Дата',
        'employee'               => 'Сотрудник',
        'project'                => 'Проект',
        'task'                   => 'Задача',
        'description'            => 'Описание',
        'time-spent'             => 'Time Spent',
        'time-spent-helper-text' => 'Time spent в часы (Eg. 1.5 часы means 1 час 30 минуты)',
    ],

    'table' => [
        'columns' => [
            'date'        => 'Дата',
            'employee'    => 'Сотрудник',
            'project'     => 'Проект',
            'task'        => 'Задача',
            'description' => 'Описание',
            'time-spent'  => 'Time Spent',
            'created-at'  => 'Создано',
            'updated-at'  => 'Обновлено',
        ],

        'groups' => [
            'date'       => 'Дата',
            'employee'   => 'Сотрудник',
            'project'    => 'Проект',
            'task'       => 'Задача',
            'creator'    => 'Creator',
        ],

        'filters' => [
            'date-from'  => 'Дата От',
            'date-until' => 'Дата До',
            'employee'   => 'Сотрудник',
            'project'    => 'Проект',
            'task'       => 'Задача',
            'creator'    => 'Creator',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Табель обновлено',
                    'body'  => 'табель есть обновлено successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Табель удалено',
                    'body'  => 'табель есть удалено successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Табели удалено',
                    'body'  => 'табели есть удалено successfully.',
                ],
            ],
        ],
    ],
];
