<?php

return [
    'heading' => [
        'title' => 'Отгул и отпуск Requests',
    ],

    'modal-actions' => [
        'edit' => [
            'title'                         => 'Изменить',
            'duration-display'              => ':count working день|:count working дни',
            'duration-display-with-weekend' => ':count working день (+ :weekend weekend день)|:count working дни (+ :weekend weekend дни)',

            'notification' => [
                'title' => 'Отгул и отпуск Обновлено',
                'body'  => 'Ваш отгул и отпуск request есть обновлено successfully.',
            ],
        ],

        'delete' => [
            'title' => 'Удалить',
        ],
    ],

    'config' => [
        'button-text' => [
            'today' => 'Сегодня',
            'month' => 'Месяц',
            'week'  => 'Неделя',
            'list'  => 'Список',
        ],
    ],

    'view-action' => [
        'title'       => 'Просмотр',
        'description' => 'Просмотр Отгул и отпуск Request',
    ],

    'notifications' => [
        'employee-not-found' => [
            'title' => 'Сотрудник Не Найдено',
            'body'  => 'Пожалуйста добавить сотрудник до ваш профиль до requesting отгул и отпуск.',
        ],

        'error' => [
            'title' => 'Something went wrong',
            'body'  => 'Мы could не process ваш отгул и отпуск request. Пожалуйста попробуйте снова.',
        ],
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

            'success' => [
                'notification' => [
                    'title' => 'Отгул и отпуск Создано',
                    'body'  => 'Ваш отгул и отпуск request есть создано successfully.',
                ],
            ],
        ],
    ],

    'form' => [
        'title'       => 'Отгул и отпуск Request',
        'description' => 'Создать или изменить ваш отгул и отпуск request с следующий подробности:',

        'fields' => [
            'time-off-type'             => 'Отгул и отпуск Тип',
            'time-off-type-placeholder' => 'Выбрать отгул и отпуск тип',
            'time-off-type-helper'      => 'Выбрать тип отгул и отпуск вы requesting.',
            'request-date-from'         => 'Request Дата От',
            'request-date-to'           => 'Request Дата До',
            'period'                    => 'Период',
            'half-day'                  => 'Half День',
            'half-day-helper'           => 'Toggle для half-день отпуск.',
            'requested-days'            => 'Requested (Дни/Часы)',
            'description'               => 'Описание',
            'description-placeholder'   => 'Нет описание provided',
            'description-helper'        => 'Provide brief описание ваш отгул и отпуск request.',
            'duration'                  => 'Duration',
            'please-select-dates'       => 'Пожалуйста выбрать request дата от и до.',
        ],
    ],

    'infolist' => [
        'title'       => 'Отгул и отпуск Подробности',
        'description' => 'Здесь подробности ваш отгул и отпуск request:',
        'entries'     => [
            'time-off-type'           => 'Отгул и отпуск Тип',
            'request-date-from'       => 'Request Дата От',
            'request-date-to'         => 'Request Дата До',
            'description'             => 'Описание',
            'description-placeholder' => 'Нет описание provided',
            'duration'                => 'Duration',
            'status'                  => 'Статус',
        ],
    ],

    'events' => [
        'title' => ':name Вкл. :status: :days день(s)',
    ],
];
