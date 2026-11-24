<?php

return [
    'navigation' => [
        'title' => 'Проекты',
    ],

    'global-search' => [
        'project-manager' => 'Руководитель проекта',
        'customer'        => 'Клиент',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Общее',

                'fields' => [
                    'name'             => 'Название',
                    'name-placeholder' => 'Название проекта...',
                    'description'      => 'Описание',
                ],
            ],

            'additional' => [
                'title' => 'Дополнительная информация',

                'fields' => [
                    'project-manager'             => 'Руководитель проекта',
                    'customer'                    => 'Клиент',
                    'start-date'                  => 'Дата начала',
                    'end-date'                    => 'Дата окончания',
                    'allocated-hours'             => 'Выделено часов',
                    'allocated-hours-helper-text' => 'В часах (например, 1,5 часа — это 1 час 30 минут)',
                    'tags'                        => 'Теги',
                    'company'                     => 'Компания',
                ],
            ],

            'settings' => [
                'title' => 'Настройки',

                'fields' => [
                    'visibility'                   => 'Видимость',
                    'visibility-hint-tooltip'      => 'Дайте сотрудникам доступ к проекту или задачам, добавив их в подписчики. Они автоматически получат доступ ко всем назначенным им задачам.',
                    'private-description'          => 'Только приглашённые внутренние пользователи.',
                    'internal-description'         => 'Видят все внутренние пользователи.',
                    'public-description'           => 'Приглашённые пользователи портала и все внутренние пользователи.',
                    'time-management'              => 'Учёт времени',
                    'allow-timesheets'             => 'Разрешить табели',
                    'allow-timesheets-helper-text' => 'Записывайте время по задачам и отслеживайте ход работ',
                    'task-management'              => 'Управление задачами',
                    'allow-milestones'             => 'Разрешить вехи',
                    'allow-milestones-helper-text' => 'Отслеживайте ключевые вехи, важные для успеха.',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'            => 'Название',
            'customer'        => 'Клиент',
            'start-date'      => 'Дата начала',
            'end-date'        => 'Дата окончания',
            'planned-date'    => 'Плановая дата',
            'remaining-hours' => 'Осталось часов',
            'project-manager' => 'Руководитель проекта',
        ],

        'groups' => [
            'stage'           => 'Этап',
            'project-manager' => 'Руководитель проекта',
            'customer'        => 'Клиент',
            'created-at'      => 'Дата создания',
        ],

        'filters' => [
            'name'             => 'Название',
            'visibility'       => 'Видимость',
            'start-date'       => 'Дата начала',
            'end-date'         => 'Дата окончания',
            'allow-timesheets' => 'Разрешить табели',
            'allow-milestones' => 'Разрешить вехи',
            'allocated-hours'  => 'Выделено часов',
            'created-at'       => 'Дата создания',
            'updated-at'       => 'Дата изменения',
            'stage'            => 'Этап',
            'customer'         => 'Клиент',
            'project-manager'  => 'Руководитель проекта',
            'company'          => 'Компания',
            'creator'          => 'Автор',
            'tags'             => 'Теги',
        ],

        'actions' => [
            'tasks'      => 'Задачи: :count',
            'milestones' => 'Выполнено вех: :completed из :all',

            'restore' => [
                'notification' => [
                    'title' => 'Проект восстановлен',
                    'body'  => 'Проект успешно восстановлен.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Проект удалён',
                    'body'  => 'Проект успешно удалён.',
                ],
            ],

            'force-delete' => [

                'notification' => [

                    'success' => [
                        'title' => 'Проект удалён безвозвратно',
                        'body'  => 'Проект удалён безвозвратно.',
                    ],

                    'error' => [
                        'title' => 'Проект нельзя удалить безвозвратно',
                        'body'  => 'Проект связан с другими записями.',
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
                    'name-placeholder' => 'Название проекта...',
                    'description'      => 'Описание',
                ],
            ],

            'additional' => [
                'title' => 'Дополнительная информация',

                'entries' => [
                    'project-manager'        => 'Руководитель проекта',
                    'customer'               => 'Клиент',
                    'project-timeline'       => 'Сроки проекта',
                    'allocated-hours'        => 'Выделено часов',
                    'allocated-hours-suffix' => ' ч',
                    'remaining-hours'        => 'Осталось часов',
                    'remaining-hours-suffix' => ' ч',
                    'current-stage'          => 'Текущий этап',
                    'tags'                   => 'Теги',
                ],
            ],

            'statistics' => [
                'title' => 'Статистика',

                'entries' => [
                    'total-tasks'         => 'Всего задач',
                    'milestones-progress' => 'Ход по вехам',
                ],
            ],

            'record-information' => [
                'title' => 'Сведения о записи',

                'entries' => [
                    'created-at'   => 'Дата создания',
                    'created-by'   => 'Создал',
                    'last-updated' => 'Последнее изменение',
                ],
            ],

            'settings' => [
                'title' => 'Настройки проекта',

                'entries' => [
                    'visibility'         => 'Видимость',
                    'timesheets-enabled' => 'Табели включены',
                    'milestones-enabled' => 'Вехи включены',
                ],
            ],
        ],
    ],
];
