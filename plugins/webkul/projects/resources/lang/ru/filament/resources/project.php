<?php

return [
    'navigation' => [
        'title' => 'Проекты',
    ],

    'global-search' => [
        'project-manager' => 'Проект Руководитель',
        'customer'        => 'Клиент',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Общее',

                'fields' => [
                    'name'             => 'Название',
                    'name-placeholder' => 'Проект Название...',
                    'description'      => 'Описание',
                ],
            ],

            'additional' => [
                'title' => 'Additional Сведения',

                'fields' => [
                    'project-manager'             => 'Проект Руководитель',
                    'customer'                    => 'Клиент',
                    'start-date'                  => 'Начать Дата',
                    'end-date'                    => 'Конец Дата',
                    'allocated-hours'             => 'Allocated Часы',
                    'allocated-hours-helper-text' => 'В часы (Eg. 1.5 часы means 1 час 30 минуты)',
                    'tags'                        => 'Метки',
                    'company'                     => 'Компания',
                ],
            ],

            'settings' => [
                'title' => 'Настройки',

                'fields' => [
                    'visibility'                   => 'Visibility',
                    'visibility-hint-tooltip'      => 'Разрешить сотрудники до access ваш проект или задачи от adding их как followers. Они автоматически gain access до любой задачи назначен до их..',
                    'private-description'          => 'Invited внутренний пользователи только.',
                    'internal-description'         => 'Все внутренний пользователи можно see.',
                    'public-description'           => 'Invited портал пользователи и все внутренний пользователи.',
                    'time-management'              => 'Time Management',
                    'allow-timesheets'             => 'Разрешить Табели',
                    'allow-timesheets-helper-text' => 'Log time вкл. задачи и track ход',
                    'task-management'              => 'Задача Management',
                    'allow-milestones'             => 'Разрешить Milestones',
                    'allow-milestones-helper-text' => 'Monitor key milestones тот essential для achieving успешно.',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'            => 'Название',
            'customer'        => 'Клиент',
            'start-date'      => 'Начать Дата',
            'end-date'        => 'Конец Дата',
            'planned-date'    => 'Запланировано Дата',
            'remaining-hours' => 'Осталось Часы',
            'project-manager' => 'Проект Руководитель',
        ],

        'groups' => [
            'stage'           => 'Этап',
            'project-manager' => 'Проект Руководитель',
            'customer'        => 'Клиент',
            'created-at'      => 'Создано',
        ],

        'filters' => [
            'name'             => 'Название',
            'visibility'       => 'Visibility',
            'start-date'       => 'Начать Дата',
            'end-date'         => 'Конец Дата',
            'allow-timesheets' => 'Разрешить Табели',
            'allow-milestones' => 'Разрешить Milestones',
            'allocated-hours'  => 'Allocated Часы',
            'created-at'       => 'Создано',
            'updated-at'       => 'Обновлено',
            'stage'            => 'Этап',
            'customer'         => 'Клиент',
            'project-manager'  => 'Проект Руководитель',
            'company'          => 'Компания',
            'creator'          => 'Creator',
            'tags'             => 'Метки',
        ],

        'actions' => [
            'tasks'      => ':count Задачи',
            'milestones' => ':completed milestones завершено вне :all',

            'restore' => [
                'notification' => [
                    'title' => 'Проект restored',
                    'body'  => 'проект есть restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Проект удалено',
                    'body'  => 'проект есть удалено successfully.',
                ],
            ],

            'force-delete' => [

                'notification' => [

                    'success' => [
                        'title' => 'Проект permanently удалено',
                        'body'  => 'проект есть permanently удалено successfully.',
                    ],

                    'error' => [
                        'title' => 'Проект нельзя быть permanently удалено',
                        'body'  => 'проект associated с другое записи.',
                    ],

                ],
            ],

        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Общее',

                'entries' => [
                    'name'             => 'Название',
                    'name-placeholder' => 'Проект Название...',
                    'description'      => 'Описание',
                ],
            ],

            'additional' => [
                'title' => 'Additional Сведения',

                'entries' => [
                    'project-manager'        => 'Проект Руководитель',
                    'customer'               => 'Клиент',
                    'project-timeline'       => 'Проект Timeline',
                    'allocated-hours'        => 'Allocated Часы',
                    'allocated-hours-suffix' => ' Часы',
                    'remaining-hours'        => 'Осталось Часы',
                    'remaining-hours-suffix' => ' Часы',
                    'current-stage'          => 'Текущий Этап',
                    'tags'                   => 'Метки',
                ],
            ],

            'statistics' => [
                'title' => 'Statistics',

                'entries' => [
                    'total-tasks'         => 'Итого Задачи',
                    'milestones-progress' => 'Milestones Ход',
                ],
            ],

            'record-information' => [
                'title' => 'Запись Сведения',

                'entries' => [
                    'created-at'   => 'Создано',
                    'created-by'   => 'Создано От',
                    'last-updated' => 'Последний Обновлено',
                ],
            ],

            'settings' => [
                'title' => 'Проект Настройки',

                'entries' => [
                    'visibility'         => 'Visibility',
                    'timesheets-enabled' => 'Табели Включено',
                    'milestones-enabled' => 'Milestones Включено',
                ],
            ],
        ],
    ],
];
