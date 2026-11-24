<?php

return [
    'title' => 'Рабочие графики',

    'navigation' => [
        'title' => 'Рабочие графики',
        'group' => 'Сотрудник',
    ],

    'groups' => [
        'status'     => 'Статус',
        'created-by' => 'Создал',
        'created-at' => 'Создано',
        'updated-at' => 'Изменено',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Общие сведения',
                'fields' => [
                    'name'                  => 'Название',
                    'schedule-name'         => 'Название графика',
                    'schedule-name-tooltip' => 'Дайте графику понятное название.',
                    'timezone'              => 'Часовой пояс',
                    'timezone-tooltip'      => 'Выберите часовой пояс рабочего графика.',
                    'company'               => 'Компания',
                ],
            ],

            'configuration' => [
                'title'  => 'Настройка рабочего времени',
                'fields' => [
                    'hours-per-day'                   => 'Часов в день',
                    'hours-per-day-suffix'            => 'ч',
                    'full-time-required-hours'        => 'Часов для полной занятости',
                    'full-time-required-hours-suffix' => 'ч в неделю',
                ],
            ],

            'flexibility' => [
                'title'  => 'Гибкость',
                'fields' => [
                    'status'                     => 'Статус',
                    'two-weeks-calendar'         => 'Двухнедельный график',
                    'two-weeks-calendar-tooltip' => 'Включить чередующийся двухнедельный график.',
                    'flexible-hours'             => 'Гибкий график',
                    'flexible-hours-tooltip'     => 'Разрешить сотрудникам гибкое рабочее время.',
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
            'updated-at'     => 'Изменено',
        ],

        'filters' => [
            'company'           => 'Компания',
            'is-active'         => 'Статус',
            'two-week-calendar' => 'Двухнедельный график',
            'flexible-hours'    => 'Гибкий график',
            'timezone'          => 'Часовой пояс',
            'name'              => 'Название графика',
            'attendance'        => 'Посещаемость',
            'created-by'        => 'Создал',
            'daily-hours'       => 'Часов в день',
            'updated-at'        => 'Изменено',
            'created-at'        => 'Создано',
        ],

        'groups' => [
            'name'           => 'Название графика',
            'status'         => 'Статус',
            'timezone'       => 'Часовой пояс',
            'flexible-hours' => 'Гибкий график',
            'daily-hours'    => 'Часов в день',
            'created-by'     => 'Создал',
            'created-at'     => 'Создано',
            'updated-at'     => 'Изменено',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'График восстановлен',
                    'body'  => 'График восстановлен.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'График удалён',
                    'body'  => 'График удалён.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'График удалён навсегда',
                    'body'  => 'График удалён навсегда.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Графики восстановлены',
                    'body'  => 'Графики восстановлены.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Графики удалены',
                    'body'  => 'Графики удалены.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Графики удалены навсегда',
                    'body'  => 'Графики удалены навсегда.',
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
                    'schedule-name-tooltip' => 'Дайте графику понятное название.',
                    'timezone'              => 'Часовой пояс',
                    'timezone-tooltip'      => 'Выберите часовой пояс рабочего графика.',
                    'company'               => 'Компания',
                ],
            ],

            'configuration' => [
                'title'   => 'Настройка рабочего времени',
                'entries' => [
                    'hours-per-day'                   => 'Часов в день',
                    'hours-per-day-suffix'            => ' ч',
                    'full-time-required-hours'        => 'Часов для полной занятости',
                    'full-time-required-hours-suffix' => ' ч в неделю',
                ],
            ],

            'flexibility' => [
                'title'   => 'Гибкость',
                'entries' => [
                    'status'                     => 'Статус',
                    'two-weeks-calendar'         => 'Двухнедельный график',
                    'two-weeks-calendar-tooltip' => 'Включить чередующийся двухнедельный график.',
                    'flexible-hours'             => 'Гибкий график',
                    'flexible-hours-tooltip'     => 'Разрешить сотрудникам гибкое рабочее время.',
                ],
            ],
        ],
    ],
];
