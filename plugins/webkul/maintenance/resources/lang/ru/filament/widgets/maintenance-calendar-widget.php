<?php

return [
    'heading' => [
        'title' => 'Календарь обслуживания',
    ],

    'config' => [
        'button-text' => [
            'today' => 'Сегодня',
            'year'  => 'Год',
            'month' => 'Месяц',
            'week'  => 'Неделя',
            'list'  => 'Список',
        ],
    ],

    'header-actions' => [
        'create' => [
            'label'         => 'Новая заявка',
            'modal-heading' => 'Новая заявка на обслуживание',
            'notification'  => [
                'success' => [
                    'title' => 'Заявка на обслуживание создана',
                    'body'  => 'Заявка на обслуживание успешно создана.',
                ],
                'error' => [
                    'title' => 'Не удалось создать заявку на обслуживание',
                    'body'  => 'Сначала создайте этап и команду обслуживания.',
                ],
            ],
        ],
    ],

    'view-action' => [
        'label' => 'Просмотр',
    ],

    'modal-actions' => [
        'edit' => [
            'label' => 'Изменить',
        ],
    ],

    'form' => [
        'fields' => [
            'subject'      => 'Тема',
            'scheduled-at' => 'Запланировано на',
        ],
    ],

    'infolist' => [
        'title'   => 'Заявка на обслуживание',
        'entries' => [
            'subject'          => 'Тема',
            'date'             => 'Дата',
            'time'             => 'Время',
            'technician'       => 'Техник',
            'priority'         => 'Приоритет',
            'maintenance-type' => 'Тип обслуживания',
            'stage'            => 'Этап',
        ],
    ],
];
