<?php

return [
    'title' => 'Отгул и отпуск',

    'model-label' => 'Отгул и отпуск',

    'navigation' => [
        'title' => 'Отгул и отпуск',
    ],

    'global-search' => [
        'employee'      => 'Сотрудник',
        'time-off-type' => 'Отгул и отпуск Тип',
        'date-from'     => 'Дата От',
        'date-to'       => 'Дата До',
    ],

    'form' => [
        'fields' => [
            'employee-name'     => 'Сотрудник Название',
            'department-name'   => 'Отдел Название',
            'time-off-type'     => 'Отгул и отпуск Тип',
            'date'              => 'Дата',
            'dates'             => 'Dates',
            'request-date-from' => 'Request Дата От',
            'request-date-to'   => 'Request Дата До',
            'description'       => 'Описание',
            'period'            => 'Период',
            'half-day'          => 'Half День',
            'requested-days'    => 'Requested (Дни/Часы)',
            'description'       => 'Описание',
            'attachment'        => 'Вложение',
            'day'               => ':day день',
            'days'              => ':days день(s)',
        ],
    ],

    'table' => [
        'columns' => [
            'employee-name'  => 'Сотрудник',
            'time-off-type'  => 'Отгул и отпуск Тип',
            'description'    => 'Описание',
            'date-from'      => 'Дата От',
            'date-to'        => 'Дата До',
            'duration'       => 'Duration',
            'status'         => 'Статус',
        ],

        'groups' => [
            'employee-name' => 'Сотрудник',
            'time-off-type' => 'Отгул и отпуск Тип',
            'status'        => 'Статус',
            'start-date'    => 'Начать Дата',
            'start-to'      => 'Конец Дата',
            'updated-at'    => 'Обновлено',
            'created-at'    => 'Создано',
        ],

        'actions' => [
            'approve' => [
                'title' => [
                    'validate' => 'Validate',
                    'approve'  => 'Утвердить',
                ],
                'notification' => [
                    'title' => 'Отгул и отпуск утверждён',
                    'body'  => 'отгул и отпуск есть утверждён successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Отгул и отпуск удалено',
                    'body'  => 'отгул и отпуск есть удалено successfully.',
                ],
            ],

            'refused' => [
                'title'        => 'Отказать',
                'notification' => [
                    'title' => 'Отгул и отпуск отказано',
                    'body'  => 'отгул и отпуск есть отказано successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Отгул и отпуск удалено',
                    'body'  => 'отгул и отпуск есть удалено successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'employee-name'     => 'Сотрудник Название',
            'department-name'   => 'Отдел Название',
            'time-off-type'     => 'Отгул и отпуск Тип',
            'date'              => 'Дата',
            'dates'             => 'Dates',
            'request-date-from' => 'Request Дата От',
            'request-date-to'   => 'Request Дата До',
            'description'       => 'Описание',
            'period'            => 'Период',
            'half-day'          => 'Half День',
            'requested-days'    => 'Requested (Дни/Часы)',
            'attachment'        => 'Вложение',
            'day'               => ':day день',
            'days'              => ':days день(s)',
            'date-from'         => 'Дата От',
            'date-to'           => 'Дата До',
            'status'            => 'Статус',
        ],
    ],
];
