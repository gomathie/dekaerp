<?php

return [
    'form' => [
        'date'                   => 'Дата',
        'employee'               => 'Сотрудник',
        'description'            => 'Описание',
        'time-spent'             => 'Затраченное время',
        'time-spent-helper-text' => 'Затраченное время в часах (например, 1,5 часа — это 1 час 30 минут)',
    ],

    'table' => [
        'header-actions' => [
            'create' => [
                'label' => 'Добавить запись табеля',

                'notification' => [
                    'title' => 'Запись табеля создана',
                    'body'  => 'Запись табеля успешно создана.',
                ],
            ],
        ],

        'columns' => [
            'date'                   => 'Дата',
            'employee'               => 'Сотрудник',
            'description'            => 'Описание',
            'time-spent'             => 'Затраченное время',
            'time-spent-on-subtasks' => 'Время на подзадачи',
            'total-time-spent'       => 'Всего затрачено времени',
            'remaining-time'         => 'Оставшееся время',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Запись табеля обновлена',
                    'body'  => 'Запись табеля успешно обновлена.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Запись табеля удалена',
                    'body'  => 'Запись табеля успешно удалена.',
                ],
            ],
        ],
    ],
];
