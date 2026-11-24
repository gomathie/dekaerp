<?php

return [
    'title'      => 'План начисления',
    'navigation' => [
        'title' => 'План начисления',
    ],

    'form' => [
        'fields' => [
            'name'                    => 'Название',
            'is-based-on-worked-time' => 'На основе отработанного времени',
            'accrued-gain-time'       => 'Момент начисления',
            'carry-over-time'         => 'Время переноса',
            'carry-over-date'         => 'Дата переноса',
            'status'                  => 'Статус',
        ],
    ],

    'table' => [
        'columns' => [
            'name'   => 'Название',
            'levels' => 'Уровни',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'План начисления удалён',
                    'body'  => 'План начисления успешно удалён.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'План начисления удалён',
                    'body'  => 'План начисления успешно удалён.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'basic-information' => 'Основная информация',
        ],

        'entries' => [
            'name'                    => 'Название',
            'is-based-on-worked-time' => 'На основе отработанного времени',
            'accrued-gain-time'       => 'Момент начисления',
            'carry-over-time'         => 'Время переноса',
            'carry-over-day'          => 'День переноса',
            'carry-over-month'        => 'Месяц переноса',
        ],
    ],
];
