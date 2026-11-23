<?php

return [
    'form' => [
        'date'                   => 'Дата',
        'employee'               => 'Сотрудник',
        'description'            => 'Описание',
        'time-spent'             => 'Time Spent',
        'time-spent-helper-text' => 'Time spent в часы (Eg. 1.5 часы means 1 час 30 минуты)',
    ],

    'table' => [
        'header-actions' => [
            'create' => [
                'label' => 'Добавить Табель',

                'notification' => [
                    'title' => 'Табель создано',
                    'body'  => 'табель есть создано successfully.',
                ],
            ],
        ],

        'columns' => [
            'date'                   => 'Дата',
            'employee'               => 'Сотрудник',
            'description'            => 'Описание',
            'time-spent'             => 'Time Spent',
            'time-spent-on-subtasks' => 'Time Spent вкл. Subtasks',
            'total-time-spent'       => 'Итого Time Spent',
            'remaining-time'         => 'Осталось Time',
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
    ],
];
