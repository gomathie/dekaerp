<?php

return [
    'title' => 'Варианты',

    'form' => [
        'date'                   => 'Дата',
        'employee'               => 'Сотрудник',
        'description'            => 'Описание',
        'time-spent'             => 'Затрачено времени',
        'time-spent-helper-text' => 'Затраченное время в часах (напр. 1,5 часа — это 1 час 30 минут)',
    ],

    'table' => [
        'columns' => [
            'date'                   => 'Дата',
            'employee'               => 'Сотрудник',
            'description'            => 'Описание',
            'time-spent'             => 'Затрачено времени',
            'time-spent-on-subtasks' => 'Время на подзадачи',
            'total-time-spent'       => 'Всего затрачено времени',
            'remaining-time'         => 'Оставшееся время',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Вариант удалён',
                    'body'  => 'Вариант успешно удалён.',
                ],
            ],
        ],
    ],
];
