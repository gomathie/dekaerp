<?php

return [
    'navigation' => [
        'title' => 'Должность Positions',
        'group' => 'Должность Positions',
    ],

    'global-search' => [
        'name'            => 'Должность Должность',
        'department'      => 'Отдел',
        'employment-type' => 'Employment Тип',
        'company'         => 'Компания',
        'created-by'      => 'Создано От',
    ],

    'form' => [
        'sections' => [
            'employment-information' => [
                'title' => 'Employment Сведения',

                'fields' => [
                    'job-position-title'         => 'Должность Должность Заголовок',
                    'job-position-title-tooltip' => 'Введите official должность должность заголовок',
                    'department'                 => 'Отдел',
                    'department-modal-title'     => 'Отдел Создать',
                    'job-location'               => 'Должность Место',
                    'industry'                   => 'Industry',
                    'company'                    => 'Компания',
                    'employment-type'            => 'Employment Тип',
                    'interviewers'               => 'Interviewers',
                    'recruiter'                  => 'Recruiter',
                    'manager'                    => 'Руководитель',
                    'company-modal-title'        => 'Создать Компания',
                ],
            ],

            'job-description' => [
                'title' => 'Должность Описание',

                'fields' => [
                    'job-description'  => 'Должность Описание',
                    'job-requirements' => 'Должность Requirements',
                ],
            ],

            'workforce-planning' => [
                'title' => 'Workforce Planning',

                'fields' => [
                    'recruitment-target' => 'Подбор Назначение',
                    'date-from'          => 'Дата От',
                    'date-to'            => 'Дата До',
                    'expected-skills'    => 'Ожидается Skills',
                    'employment-type'    => 'Employment Тип',
                    'status'             => 'Статус',
                ],
            ],

            'position-status' => [
                'title' => 'Должность Статус',

                'fields' => [
                    'status' => 'Статус',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'id'                 => 'Ид.',
            'name'               => 'Должность Должность',
            'department'         => 'Отдел',
            'job-position'       => 'Должность Должность',
            'company'            => 'Компания',
            'expected-employees' => 'Ожидается Сотрудники',
            'current-employees'  => 'Текущий Сотрудники',
            'status'             => 'Статус',
            'created-by'         => 'Создано От',
            'created-at'         => 'Создано',
            'updated-at'         => 'Обновлено',
        ],

        'filters' => [
            'department'      => 'Отдел',
            'employment-type' => 'Employment Тип',
            'job-position'    => 'Должность Должность',
            'company'         => 'Компания',
            'status'          => 'Статус',
            'created-by'      => 'Создано От',
            'updated-at'      => 'Обновлено',
            'created-at'      => 'Создано',
        ],

        'groups' => [
            'job-position'    => 'Должность Должность',
            'company'         => 'Компания',
            'department'      => 'Отдел',
            'employment-type' => 'Employment Тип',
            'created-by'      => 'Создано От',
            'created-at'      => 'Создано',
            'updated-at'      => 'Обновлено',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Должность Должность restored',
                    'body'  => 'Должность Должность есть restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Должность Должность удалено',
                    'body'  => 'Должность Должность есть удалено successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Должность Positions restored',
                    'body'  => 'Должность Positions есть restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Должность Positions удалено',
                    'body'  => 'Должность Positions есть удалено successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Должность Positions force удалено',
                    'body'  => 'Должность Positions есть force удалено successfully.',
                ],
            ],
        ],

        'empty-state-actions' => [
            'create' => [
                'notification' => [
                    'title' => 'Должность Positions',
                    'body'  => 'Должность Positions есть создано successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'employment-information' => [
                'title' => 'Employment Сведения',

                'entries' => [
                    'job-position-title' => 'Должность Должность Заголовок',
                    'department'         => 'Отдел',
                    'company'            => 'Компания',
                    'employment-type'    => 'Employment Тип',
                    'job-location'       => 'Должность Место',
                    'industry'           => 'Industry',
                    'manager'            => 'Руководитель',
                    'recruiter'          => 'Recruiter',
                    'interviewers'       => 'Interviewers',
                ],
            ],
            'job-description' => [
                'title' => 'Должность Описание',

                'entries' => [
                    'job-description'  => 'Должность Описание',
                    'job-requirements' => 'Должность Requirements',
                ],
            ],
            'work-planning' => [
                'title' => 'Workforce Planning',

                'entries' => [
                    'expected-employees' => 'Ожидается Сотрудники',
                    'current-employees'  => 'Текущий Сотрудники',
                    'date-from'          => 'Дата От',
                    'date-to'            => 'Дата До',
                    'recruitment-target' => 'Подбор Назначение',
                ],
            ],
            'position-status' => [
                'title' => 'Должность Статус',

                'entries' => [
                    'status' => 'Статус',
                ],
            ],
        ],
    ],
];
