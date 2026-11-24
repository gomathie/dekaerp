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
        'time-spent'             => 'Затраченное время',
        'time-spent-helper-text' => 'Затраченное время в часах (например, 1,5 — это 1 час 30 минут)',
    ],

    'table' => [
        'columns' => [
            'date'        => 'Дата',
            'employee'    => 'Сотрудник',
            'project'     => 'Проект',
            'task'        => 'Задача',
            'description' => 'Описание',
            'time-spent'  => 'Затраченное время',
            'created-at'  => 'Создано',
            'updated-at'  => 'Обновлено',
        ],

        'groups' => [
            'date'       => 'Дата',
            'employee'   => 'Сотрудник',
            'project'    => 'Проект',
            'task'       => 'Задача',
            'creator'    => 'Автор',
        ],

        'filters' => [
            'date-from'  => 'Дата с',
            'date-until' => 'Дата по',
            'employee'   => 'Сотрудник',
            'project'    => 'Проект',
            'task'       => 'Задача',
            'creator'    => 'Автор',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Табель обновлён',
                    'body'  => 'Табель успешно обновлён.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Табель удалён',
                    'body'  => 'Табель успешно удалён.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Табели удалены',
                    'body'  => 'Табели успешно удалены.',
                ],
            ],
        ],
    ],
];
