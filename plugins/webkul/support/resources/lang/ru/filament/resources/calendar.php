<?php

return [
    'title' => 'Рабочие графики',

    'navigation' => [
        'title' => 'Рабочие графики',
    ],

    'groups' => [
        'status'     => 'Статус',
        'created-by' => 'Создал',
        'created-at' => 'Создано',
        'updated-at' => 'Обновлено',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Общие сведения',
                'fields' => [
                    'name'                  => 'Название',
                    'schedule-name'         => 'Название графика',
                    'schedule-name-tooltip' => 'Укажите понятное название рабочего графика.',
                    'timezone'              => 'Часовой пояс',
                    'timezone-tooltip'      => 'Выберите часовой пояс для рабочего графика.',
                    'company'               => 'Компания',
                ],
            ],

            'configuration' => [
                'title'  => 'Рабочее время',
                'fields' => [
                    'hours-per-day'                   => 'Часов в день',
                    'hours-per-day-suffix'            => 'ч',
                    'full-time-required-hours'        => 'Норма часов при полной занятости',
                    'full-time-required-hours-suffix' => 'ч в неделю',
                ],
            ],

            'flexibility' => [
                'title'  => 'Гибкость',
                'fields' => [
                    'status'                     => 'Статус',
                    'two-weeks-calendar'         => 'Двухнедельный календарь',
                    'two-weeks-calendar-tooltip' => 'Включить чередующийся двухнедельный график работы.',
                    'flexible-hours'             => 'Гибкий график',
                    'flexible-hours-tooltip'     => 'Разрешить сотрудникам гибкий график работы.',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'id'             => 'ID',
            'name'           => 'Название графика',
            'timezone'       => 'Часовой пояс',
            'company'        => 'Компания',
            'flexible-hours' => 'Гибкий график',
            'status'         => 'Статус',
            'daily-hours'    => 'Часов в день',
            'created-by'     => 'Создал',
            'created-at'     => 'Создано',
            'updated-at'     => 'Обновлено',
        ],

        'filters' => [
            'company'                  => 'Компания',
            'is-active'                => 'Статус',
            'two-week-calendar'        => 'Двухнедельный календарь',
            'flexible-hours'           => 'Гибкий график',
            'timezone'                 => 'Часовой пояс',
            'name'                     => 'Название графика',
            'attendance'               => 'Посещаемость',
            'created-by'               => 'Создал',
            'daily-hours'              => 'Часов в день',
            'full-time-required-hours' => 'Норма часов при полной занятости',
            'updated-at'               => 'Обновлено',
            'created-at'               => 'Создано',
        ],

        'groups' => [
            'name'           => 'Название графика',
            'status'         => 'Статус',
            'timezone'       => 'Часовой пояс',
            'flexible-hours' => 'Гибкий график',
            'daily-hours'    => 'Часов в день',
            'created-by'     => 'Создал',
            'created-at'     => 'Создано',
            'updated-at'     => 'Обновлено',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'График восстановлен',
                    'body'  => 'График успешно восстановлен.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'График удалён',
                    'body'  => 'График успешно удалён.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'График удалён безвозвратно',
                    'body'  => 'График успешно удалён безвозвратно.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Графики восстановлены',
                    'body'  => 'Графики успешно восстановлены.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Графики удалены',
                    'body'  => 'Графики успешно удалены.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Графики удалены безвозвратно',
                    'body'  => 'Графики успешно удалены безвозвратно.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'   => 'Общие сведения',
                'entries' => [
                    'name'                  => 'Название',
                    'schedule-name'         => 'Название графика',
                    'schedule-name-tooltip' => 'Укажите понятное название рабочего графика.',
                    'timezone'              => 'Часовой пояс',
                    'timezone-tooltip'      => 'Выберите часовой пояс для рабочего графика.',
                    'company'               => 'Компания',
                ],
            ],

            'configuration' => [
                'title'   => 'Рабочее время',
                'entries' => [
                    'hours-per-day'                   => 'Часов в день',
                    'hours-per-day-suffix'            => ' ч',
                    'full-time-required-hours'        => 'Норма часов при полной занятости',
                    'full-time-required-hours-suffix' => ' ч в неделю',
                ],
            ],

            'flexibility' => [
                'title'   => 'Гибкость',
                'entries' => [
                    'status'                     => 'Статус',
                    'two-weeks-calendar'         => 'Двухнедельный календарь',
                    'two-weeks-calendar-tooltip' => 'Включить чередующийся двухнедельный график работы.',
                    'flexible-hours'             => 'Гибкий график',
                    'flexible-hours-tooltip'     => 'Разрешить сотрудникам гибкий график работы.',
                ],
            ],
        ],
    ],
];
