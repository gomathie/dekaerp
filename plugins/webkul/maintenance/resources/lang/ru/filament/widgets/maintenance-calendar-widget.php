<?php

return [
    'heading' => [
        'title' => 'Обслуживание Календарь',
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
            'label'         => 'Новое Request',
            'modal-heading' => 'Новое Обслуживание Request',
            'notification'  => [
                'success' => [
                    'title' => 'Обслуживание request создано',
                    'body'  => 'обслуживание request есть создано successfully.',
                ],
                'error' => [
                    'title' => 'Обслуживание request could не быть создано',
                    'body'  => 'Создать обслуживание этап и команда первый.',
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
            'subject'      => 'Subject',
            'scheduled-at' => 'Scheduled В',
        ],
    ],

    'infolist' => [
        'title'   => 'Обслуживание Request',
        'entries' => [
            'subject'          => 'Subject',
            'date'             => 'Дата',
            'time'             => 'Time',
            'technician'       => 'Technician',
            'priority'         => 'Приоритет',
            'maintenance-type' => 'Обслуживание Тип',
            'stage'            => 'Этап',
        ],
    ],
];
