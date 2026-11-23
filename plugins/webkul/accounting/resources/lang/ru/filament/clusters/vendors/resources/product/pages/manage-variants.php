<?php

return [
    'title' => 'Variants',

    'form' => [
        'date'                   => 'Дата',
        'employee'               => 'Сотрудник',
        'description'            => 'Описание',
        'time-spent'             => 'Time Spent',
        'time-spent-helper-text' => 'Time spent в часы (Eg. 1.5 часы means 1 час 30 минуты)',
    ],

    'table' => [
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
            'delete' => [
                'notification' => [
                    'title' => 'Variant удалено',
                    'body'  => 'variant есть удалено successfully.',
                ],
            ],
        ],
    ],
];
