<?php

return [
    'title' => 'Working Schedules',

    'navigation' => [
        'title' => 'Working Schedules',
    ],

    'groups' => [
        'status'     => 'Статус',
        'created-by' => 'Создано От',
        'created-at' => 'Создано',
        'updated-at' => 'Обновлено',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Общее Сведения',
                'fields' => [
                    'name'                  => 'Название',
                    'schedule-name'         => 'График Название',
                    'schedule-name-tooltip' => 'Пожалуйста запись descriptive working график название.',
                    'timezone'              => 'Timezone',
                    'timezone-tooltip'      => 'Пожалуйста выбрать timezone для working график.',
                    'company'               => 'Компания',
                ],
            ],

            'configuration' => [
                'title'  => 'Work Часы Конфигурация',
                'fields' => [
                    'hours-per-day'                   => 'Часы За День',
                    'hours-per-day-suffix'            => 'Часы',
                    'full-time-required-hours'        => 'Полный Time Обязательно Часы',
                    'full-time-required-hours-suffix' => 'Часы За Неделя',
                ],
            ],

            'flexibility' => [
                'title'  => 'Flexibility',
                'fields' => [
                    'status'                     => 'Статус',
                    'two-weeks-calendar'         => 'Two Weeks Календарь',
                    'two-weeks-calendar-tooltip' => 'Включить alternating two-неделя work график.',
                    'flexible-hours'             => 'Flexible Часы',
                    'flexible-hours-tooltip'     => 'Разрешить сотрудники до есть flexible work часы.',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'id'             => 'Ид.',
            'name'           => 'График Название',
            'timezone'       => 'Timezone',
            'company'        => 'Компания',
            'flexible-hours' => 'Flexible Часы',
            'status'         => 'Статус',
            'daily-hours'    => 'Ежедневно Часы',
            'created-by'     => 'Создано От',
            'created-at'     => 'Создано',
            'updated-at'     => 'Обновлено',
        ],

        'filters' => [
            'company'                  => 'Компания',
            'is-active'                => 'Статус',
            'two-week-calendar'        => 'Two Weeks Календарь',
            'flexible-hours'           => 'Flexible Часы',
            'timezone'                 => 'Timezone',
            'name'                     => 'График Название',
            'attendance'               => 'Посещаемость',
            'created-by'               => 'Создано От',
            'daily-hours'              => 'Ежедневно Часы',
            'full-time-required-hours' => 'Полный Time Обязательно Часы',
            'updated-at'               => 'Обновлено',
            'created-at'               => 'Создано',
        ],

        'groups' => [
            'name'           => 'График Название',
            'status'         => 'Статус',
            'timezone'       => 'Timezone',
            'flexible-hours' => 'Flexible Часы',
            'daily-hours'    => 'Ежедневно Часы',
            'created-by'     => 'Создано От',
            'created-at'     => 'Создано',
            'updated-at'     => 'Обновлено',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Календарь Plan restored',
                    'body'  => 'календарь plan есть restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Календарь Plan удалено',
                    'body'  => 'календарь plan есть удалено successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Календарь Plan force удалено',
                    'body'  => 'календарь plan есть force удалено successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Календарь Plans restored',
                    'body'  => 'календарь plans есть restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Календарь Plans удалено',
                    'body'  => 'календарь plans есть удалено successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Календарь Plans force удалено',
                    'body'  => 'календарь plans есть force удалено successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'   => 'Общее Сведения',
                'entries' => [
                    'name'                  => 'Название',
                    'schedule-name'         => 'График Название',
                    'schedule-name-tooltip' => 'Пожалуйста запись descriptive working график название.',
                    'timezone'              => 'Timezone',
                    'timezone-tooltip'      => 'Пожалуйста выбрать timezone для working график.',
                    'company'               => 'Компания',
                ],
            ],

            'configuration' => [
                'title'   => 'Work Часы Конфигурация',
                'entries' => [
                    'hours-per-day'                   => 'Часы За День',
                    'hours-per-day-suffix'            => ' Часы',
                    'full-time-required-hours'        => 'Полный Time Обязательно Часы',
                    'full-time-required-hours-suffix' => ' Часы За Неделя',
                ],
            ],

            'flexibility' => [
                'title'   => 'Flexibility',
                'entries' => [
                    'status'                     => 'Статус',
                    'two-weeks-calendar'         => 'Two Weeks Календарь',
                    'two-weeks-calendar-tooltip' => 'Включить alternating two-неделя work график.',
                    'flexible-hours'             => 'Flexible Часы',
                    'flexible-hours-tooltip'     => 'Разрешить сотрудники до есть flexible work часы.',
                ],
            ],
        ],
    ],
];
