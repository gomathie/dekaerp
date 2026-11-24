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
                    'title-placeholder' => 'Заголовок задачи...',
                    'tags'              => 'Теги',
                    'name'              => 'Название',
                    'color'             => 'Цвет',
                    'description'       => 'Описание',
                    'project'           => 'Проект',
                    'status'            => 'Статус',
                    'start_date'        => 'Дата начала',
                    'end_date'          => 'Дата окончания',
                ],
            ],

            'additional' => [
                'title' => 'Дополнительная информация',
            ],

            'settings' => [
                'title' => 'Настройки',

                'fields' => [
                    'project'                     => 'Проект',
                    'milestone'                   => 'Веха',
                    'milestone-hint-text'         => 'Свяжите веху со строкой заказа на продажу, чтобы услуги оказывались автоматически по её достижении.',
                    'name'                        => 'Название',
                    'deadline'                    => 'Срок',
                    'is-completed'                => 'Завершено',
                    'customer'                    => 'Клиент',
                    'assignees'                   => 'Исполнители',
                    'allocated-hours'             => 'Выделено часов',
                    'allocated-hours-helper-text' => 'В часах (например, 1,5 часа — это 1 час 30 минут)',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'id'                  => 'ID',
            'priority'            => 'Приоритет',
            'state'               => 'Состояние',
            'new-state'           => 'Новое состояние',
            'update-state'        => 'Изменить состояние',
            'title'               => 'Заголовок',
            'project'             => 'Проект',
            'project-placeholder' => 'Личная задача',
            'milestone'           => 'Веха',
            'customer'            => 'Клиент',
            'assignees'           => 'Исполнители',
            'allocated-time'      => 'Выделенное время',
            'time-spent'          => 'Затраченное время',
            'time-remaining'      => 'Оставшееся время',
            'progress'            => 'Прогресс',
            'deadline'            => 'Срок',
            'tags'                => 'Теги',
            'stage'               => 'Этап',
        ],

        'groups' => [
            'state'      => 'Состояние',
            'project'    => 'Проект',
            'milestone'  => 'Веха',
            'customer'   => 'Клиент',
            'deadline'   => 'Срок',
            'stage'      => 'Этап',
            'created-at' => 'Дата создания',
        ],

        'filters' => [
            'title'             => 'Заголовок',
            'priority'          => 'Приоритет',
            'low'               => 'Низкий',
            'high'              => 'Высокий',
            'state'             => 'Состояние',
            'tags'              => 'Теги',
            'allocated-hours'   => 'Выделено часов',
            'total-hours-spent' => 'Всего затрачено часов',
            'remaining-hours'   => 'Осталось часов',
            'overtime'          => 'Сверхурочно',
            'progress'          => 'Прогресс',
            'deadline'          => 'Срок',
            'created-at'        => 'Дата создания',
            'updated-at'        => 'Дата изменения',
            'assignees'         => 'Исполнители',
            'customer'          => 'Клиент',
            'project'           => 'Проект',
            'stage'             => 'Этап',
            'milestone'         => 'Веха',
            'company'           => 'Компания',
            'creator'           => 'Автор',
        ],

        'actions' => [
            'update-state' => [
                'modal-heading' => 'Изменить состояние задачи',
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Задача восстановлена',
                    'body'  => 'Задача успешно восстановлена.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Задача удалена',
                    'body'  => 'Задача успешно удалена.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Задача удалена безвозвратно',
                    'body'  => 'Задача удалена безвозвратно.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Задачи восстановлены',
                    'body'  => 'Задачи успешно восстановлены.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Задачи удалены',
                    'body'  => 'Задачи успешно удалены.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Задачи удалены безвозвратно',
                    'body'  => 'Задачи удалены безвозвратно.',
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
                    'tags'        => 'Теги',
                    'priority'    => 'Приоритет',
                    'description' => 'Описание',
                ],
            ],

            'project-information' => [
                'title' => 'Сведения о проекте',

                'entries' => [
                    'project'   => 'Проект',
                    'milestone' => 'Веха',
                    'customer'  => 'Клиент',
                    'assignees' => 'Исполнители',
                    'deadline'  => 'Срок',
                    'stage'     => 'Этап',
                ],
            ],

            'time-tracking' => [
                'title' => 'Учёт времени',

                'entries' => [
                    'allocated-time'        => 'Выделенное время',
                    'time-spent'            => 'Затраченное время',
                    'time-spent-suffix'     => ' ч',
                    'time-remaining'        => 'Оставшееся время',
                    'time-remaining-suffix' => ' ч',
                    'progress'              => 'Прогресс',
                ],
            ],

            'additional-information' => [
                'title' => 'Дополнительная информация',
            ],

            'record-information' => [
                'title' => 'Сведения о записи',

                'entries' => [
                    'created-at'   => 'Дата создания',
                    'created-by'   => 'Создал',
                    'last-updated' => 'Последнее изменение',
                ],
            ],

            'statistics' => [
                'title' => 'Статистика',

                'entries' => [
                    'sub-tasks'         => 'Подзадачи',
                    'timesheet-entries' => 'Записи табеля',
                ],
            ],
        ],
    ],
];
