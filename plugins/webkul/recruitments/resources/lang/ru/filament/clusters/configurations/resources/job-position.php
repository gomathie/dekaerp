<?php

return [
    'navigation' => [
        'title' => 'Должности',
        'group' => 'Должности',
    ],

    'global-search' => [
        'name'            => 'Должность',
        'department'      => 'Отдел',
        'employment-type' => 'Тип занятости',
        'company'         => 'Компания',
        'created-by'      => 'Создал',
    ],

    'form' => [
        'sections' => [
            'employment-information' => [
                'title' => 'Сведения о занятости',

                'fields' => [
                    'job-position-title'         => 'Название должности',
                    'job-position-title-tooltip' => 'Введите официальное название должности',
                    'department'                 => 'Отдел',
                    'department-modal-title'     => 'Создать отдел',
                    'job-location'               => 'Место работы',
                    'industry'                   => 'Отрасль',
                    'company'                    => 'Компания',
                    'employment-type'            => 'Тип занятости',
                    'interviewers'               => 'Интервьюеры',
                    'recruiter'                  => 'Рекрутер',
                    'manager'                    => 'Руководитель',
                    'company-modal-title'        => 'Создать компанию',
                ],
            ],

            'job-description' => [
                'title' => 'Описание должности',

                'fields' => [
                    'job-description'  => 'Описание должности',
                    'job-requirements' => 'Требования к должности',
                ],
            ],

            'workforce-planning' => [
                'title' => 'Планирование штата',

                'fields' => [
                    'recruitment-target' => 'План найма',
                    'date-from'          => 'Дата с',
                    'date-to'            => 'Дата по',
                    'expected-skills'    => 'Требуемые навыки',
                    'employment-type'    => 'Тип занятости',
                    'status'             => 'Статус',
                ],
            ],

            'position-status' => [
                'title' => 'Статус должности',

                'fields' => [
                    'status' => 'Статус',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'id'                 => 'ID',
            'name'               => 'Должность',
            'department'         => 'Отдел',
            'job-position'       => 'Должность',
            'company'            => 'Компания',
            'expected-employees' => 'Плановая численность',
            'current-employees'  => 'Текущая численность',
            'status'             => 'Статус',
            'created-by'         => 'Создал',
            'created-at'         => 'Дата создания',
            'updated-at'         => 'Дата изменения',
        ],

        'filters' => [
            'department'      => 'Отдел',
            'employment-type' => 'Тип занятости',
            'job-position'    => 'Должность',
            'company'         => 'Компания',
            'status'          => 'Статус',
            'created-by'      => 'Создал',
            'updated-at'      => 'Дата изменения',
            'created-at'      => 'Дата создания',
        ],

        'groups' => [
            'job-position'    => 'Должность',
            'company'         => 'Компания',
            'department'      => 'Отдел',
            'employment-type' => 'Тип занятости',
            'created-by'      => 'Создал',
            'created-at'      => 'Дата создания',
            'updated-at'      => 'Дата изменения',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Должность восстановлена',
                    'body'  => 'Должность успешно восстановлена.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Должность удалена',
                    'body'  => 'Должность успешно удалена.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Должности восстановлены',
                    'body'  => 'Должности успешно восстановлены.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Должности удалены',
                    'body'  => 'Должности успешно удалены.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Должности удалены безвозвратно',
                    'body'  => 'Должности удалены безвозвратно.',
                ],
            ],
        ],

        'empty-state-actions' => [
            'create' => [
                'notification' => [
                    'title' => 'Должности',
                    'body'  => 'Должность успешно создана.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'employment-information' => [
                'title' => 'Сведения о занятости',

                'entries' => [
                    'job-position-title' => 'Название должности',
                    'department'         => 'Отдел',
                    'company'            => 'Компания',
                    'employment-type'    => 'Тип занятости',
                    'job-location'       => 'Место работы',
                    'industry'           => 'Отрасль',
                    'manager'            => 'Руководитель',
                    'recruiter'          => 'Рекрутер',
                    'interviewers'       => 'Интервьюеры',
                ],
            ],
            'job-description' => [
                'title' => 'Описание должности',

                'entries' => [
                    'job-description'  => 'Описание должности',
                    'job-requirements' => 'Требования к должности',
                ],
            ],
            'work-planning' => [
                'title' => 'Планирование штата',

                'entries' => [
                    'expected-employees' => 'Плановая численность',
                    'current-employees'  => 'Текущая численность',
                    'date-from'          => 'Дата с',
                    'date-to'            => 'Дата по',
                    'recruitment-target' => 'План найма',
                ],
            ],
            'position-status' => [
                'title' => 'Статус должности',

                'entries' => [
                    'status' => 'Статус',
                ],
            ],
        ],
    ],
];
