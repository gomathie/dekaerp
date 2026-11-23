<?php

return [
    'heading' => [
        'title' => 'Отгул и отпуск Обзор',
    ],

    'modal-actions' => [
        'edit' => [
            'title'        => 'Изменить',
            'notification' => [
                'title' => 'Отгул и отпуск Обновлено',
                'body'  => 'Отгул и отпуск request есть обновлено successfully.',
            ],
        ],

        'delete' => [
            'title' => 'Удалить',
        ],
    ],

    'view-action' => [
        'title'       => 'Просмотр',
        'description' => 'Просмотр Отгул и отпуск Request',
    ],

    'header-actions' => [
        'create' => [
            'title'       => 'Новое Отгул и отпуск',
            'description' => 'Создать Отгул и отпуск Request',

            'notification' => [
                'title' => 'Отгул и отпуск Создано',
                'body'  => 'Отгул и отпуск request есть создано successfully.',
            ],

            'employee-not-found' => [
                'notification' => [
                    'title' => 'Сотрудник Не Найдено',
                    'body'  => 'Пожалуйста добавить сотрудник до ваш профиль до creating отгул и отпуск request.',
                ],
            ],
        ],
    ],

    'form' => [
        'fields' => [
            'time-off-type'     => 'Отгул и отпуск Тип',
            'request-date-from' => 'Request Дата От',
            'request-date-to'   => 'Request Дата До',
            'period'            => 'Период',
            'half-day'          => 'Half День',
            'requested-days'    => 'Requested (Дни/Часы)',
            'description'       => 'Описание',
        ],
    ],

    'infolist' => [
        'entries' => [
            'time-off-type'           => 'Отгул и отпуск Тип',
            'request-date-from'       => 'Request Дата От',
            'request-date-to'         => 'Request Дата До',
            'description'             => 'Описание',
            'description-placeholder' => 'Нет описание provided',
            'duration'                => 'Duration',
            'status'                  => 'Статус',
        ],
    ],
];
