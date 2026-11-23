<?php

return [
    'title' => 'Задачи',

    'navigation' => [
        'title' => 'Задачи',
    ],

    'global-search' => [
        'project'   => 'Проект',
        'customer'  => 'Клиент',
        'milestone' => 'Веха',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Общее',

                'fields' => [
                    'title'             => 'Заголовок',
                    'title-placeholder' => 'Задача Заголовок...',
                    'tags'              => 'Метки',
                    'name'              => 'Название',
                    'color'             => 'Цвет',
                    'description'       => 'Описание',
                    'project'           => 'Проект',
                    'status'            => 'Статус',
                    'start_date'        => 'Начать Дата',
                    'end_date'          => 'Конец Дата',
                ],
            ],

            'additional' => [
                'title' => 'Additional Сведения',
            ],

            'settings' => [
                'title' => 'Настройки',

                'fields' => [
                    'project'                     => 'Проект',
                    'milestone'                   => 'Веха',
                    'milestone-hint-text'         => 'Автоматически deliver ваш services upon reaching веха от linking это до заказ на продажу позиция.',
                    'name'                        => 'Название',
                    'deadline'                    => 'Deadline',
                    'is-completed'                => 'Завершено',
                    'customer'                    => 'Клиент',
                    'assignees'                   => 'Assignees',
                    'allocated-hours'             => 'Allocated Часы',
                    'allocated-hours-helper-text' => 'В часы (Eg. 1.5 часы means 1 час 30 минуты)',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'id'                  => 'Ид.',
            'priority'            => 'Приоритет',
            'state'               => 'Состояние',
            'new-state'           => 'Новое Состояние',
            'update-state'        => 'Обновить Состояние',
            'title'               => 'Заголовок',
            'project'             => 'Проект',
            'project-placeholder' => 'Личный Задача',
            'milestone'           => 'Веха',
            'customer'            => 'Клиент',
            'assignees'           => 'Assignees',
            'allocated-time'      => 'Allocated Time',
            'time-spent'          => 'Time Spent',
            'time-remaining'      => 'Time Осталось',
            'progress'            => 'Ход',
            'deadline'            => 'Deadline',
            'tags'                => 'Метки',
            'stage'               => 'Этап',
        ],

        'groups' => [
            'state'      => 'Состояние',
            'project'    => 'Проект',
            'milestone'  => 'Веха',
            'customer'   => 'Клиент',
            'deadline'   => 'Deadline',
            'stage'      => 'Этап',
            'created-at' => 'Создано',
        ],

        'filters' => [
            'title'             => 'Заголовок',
            'priority'          => 'Приоритет',
            'low'               => 'Низкий',
            'high'              => 'Высокий',
            'state'             => 'Состояние',
            'tags'              => 'Метки',
            'allocated-hours'   => 'Allocated Часы',
            'total-hours-spent' => 'Итого Часы Spent',
            'remaining-hours'   => 'Осталось Часы',
            'overtime'          => 'Сверхурочные',
            'progress'          => 'Ход',
            'deadline'          => 'Deadline',
            'created-at'        => 'Создано',
            'updated-at'        => 'Обновлено',
            'assignees'         => 'Assignees',
            'customer'          => 'Клиент',
            'project'           => 'Проект',
            'stage'             => 'Этап',
            'milestone'         => 'Веха',
            'company'           => 'Компания',
            'creator'           => 'Creator',
        ],

        'actions' => [
            'update-state' => [
                'modal-heading' => 'Обновить Задача Состояние',
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Задача restored',
                    'body'  => 'задача есть restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Задача удалено',
                    'body'  => 'задача есть удалено successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Задача force удалено',
                    'body'  => 'задача есть force удалено successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Задачи restored',
                    'body'  => 'задачи есть restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Задачи удалено',
                    'body'  => 'задачи есть удалено successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Задачи force удалено',
                    'body'  => 'задачи есть force удалено successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Общее',

                'entries' => [
                    'title'       => 'Заголовок',
                    'state'       => 'Состояние',
                    'tags'        => 'Метки',
                    'priority'    => 'Приоритет',
                    'description' => 'Описание',
                ],
            ],

            'project-information' => [
                'title' => 'Проект Сведения',

                'entries' => [
                    'project'   => 'Проект',
                    'milestone' => 'Веха',
                    'customer'  => 'Клиент',
                    'assignees' => 'Assignees',
                    'deadline'  => 'Deadline',
                    'stage'     => 'Этап',
                ],
            ],

            'time-tracking' => [
                'title' => 'Time Tracking',

                'entries' => [
                    'allocated-time'        => 'Allocated Time',
                    'time-spent'            => 'Time Spent',
                    'time-spent-suffix'     => ' Часы',
                    'time-remaining'        => 'Time Осталось',
                    'time-remaining-suffix' => ' Часы',
                    'progress'              => 'Ход',
                ],
            ],

            'additional-information' => [
                'title' => 'Additional Сведения',
            ],

            'record-information' => [
                'title' => 'Запись Сведения',

                'entries' => [
                    'created-at'   => 'Создано',
                    'created-by'   => 'Создано От',
                    'last-updated' => 'Последний Обновлено',
                ],
            ],

            'statistics' => [
                'title' => 'Statistics',

                'entries' => [
                    'sub-tasks'         => 'Sub Задачи',
                    'timesheet-entries' => 'Табель Проводки',
                ],
            ],
        ],
    ],
];
