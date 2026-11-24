<?php

return [
    'title' => 'Отпуск',

    'model-label' => 'Отпуск',

    'navigation' => [
        'title' => 'Отпуск',
    ],

    'global-search' => [
        'employee'      => 'Сотрудник',
        'time-off-type' => 'Тип отпуска',
        'date-from'     => 'Дата с',
        'date-to'       => 'Дата по',
    ],

    'form' => [
        'fields' => [
            'employee-name'     => 'Имя сотрудника',
            'department-name'   => 'Отдел',
            'time-off-type'     => 'Тип отпуска',
            'date'              => 'Дата',
            'dates'             => 'Даты',
            'request-date-from' => 'Запрошено с',
            'request-date-to'   => 'Запрошено по',
            'description'       => 'Описание',
            'period'            => 'Период',
            'half-day'          => 'Полдня',
            'requested-days'    => 'Запрошено (дни/часы)',
            'description'       => 'Описание',
            'attachment'        => 'Вложение',
            'day'               => ':day дн.',
            'days'              => ':days дн.',
        ],
    ],

    'table' => [
        'columns' => [
            'employee-name'  => 'Сотрудник',
            'time-off-type'  => 'Тип отпуска',
            'description'    => 'Описание',
            'date-from'      => 'Дата с',
            'date-to'        => 'Дата по',
            'duration'       => 'Продолжительность',
            'status'         => 'Статус',
        ],

        'groups' => [
            'employee-name' => 'Сотрудник',
            'time-off-type' => 'Тип отпуска',
            'status'        => 'Статус',
            'start-date'    => 'Дата начала',
            'start-to'      => 'Дата окончания',
            'updated-at'    => 'Обновлено',
            'created-at'    => 'Создано',
        ],

        'actions' => [
            'approve' => [
                'title' => [
                    'validate' => 'Подтвердить',
                    'approve'  => 'Утвердить',
                ],
                'notification' => [
                    'title' => 'Отпуск утверждён',
                    'body'  => 'Отпуск успешно утверждён.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Отпуск удалён',
                    'body'  => 'Отпуск успешно удалён.',
                ],
            ],

            'refused' => [
                'title'        => 'Отклонить',
                'notification' => [
                    'title' => 'Отпуск отклонён',
                    'body'  => 'Отпуск успешно отклонён.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Отпуск удалён',
                    'body'  => 'Отпуск успешно удалён.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'employee-name'     => 'Имя сотрудника',
            'department-name'   => 'Отдел',
            'time-off-type'     => 'Тип отпуска',
            'date'              => 'Дата',
            'dates'             => 'Даты',
            'request-date-from' => 'Запрошено с',
            'request-date-to'   => 'Запрошено по',
            'description'       => 'Описание',
            'period'            => 'Период',
            'half-day'          => 'Полдня',
            'requested-days'    => 'Запрошено (дни/часы)',
            'attachment'        => 'Вложение',
            'day'               => ':day дн.',
            'days'              => ':days дн.',
            'date-from'         => 'Дата с',
            'date-to'           => 'Дата по',
            'status'            => 'Статус',
        ],
    ],
];
