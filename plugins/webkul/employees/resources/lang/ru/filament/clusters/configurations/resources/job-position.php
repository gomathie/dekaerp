<?php

return [
    'title' => 'Должности',

    'navigation' => [
        'title' => 'Должности',
        'group' => 'Подбор персонала',
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
                    'company-modal-title'        => 'Создать компанию',
                    'job-location'               => 'Место работы',
                    'industry'                   => 'Отрасль',
                    'company'                    => 'Компания',
                    'employment-type'            => 'Тип занятости',
                    'recruiter'                  => 'Рекрутер',
                    'interviewer'                => 'Интервьюер',
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
                'title' => 'Планирование персонала',

                'fields' => [
                    'recruitment-target'         => 'План найма',
                    'date-from'                  => 'Дата с',
                    'date-to'                    => 'Дата по',
                    'expected-skills'            => 'Ожидаемые навыки',
                    'employment-type'            => 'Тип занятости',
                    'status'                     => 'Статус',
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
            'expected-employees' => 'Ожидается сотрудников',
            'current-employees'  => 'Текущих сотрудников',
            'status'             => 'Статус',
            'created-by'         => 'Создал',
            'created-at'         => 'Создано',
            'updated-at'         => 'Изменено',
        ],

        'filters' => [
            'department'      => 'Отдел',
            'employment-type' => 'Тип занятости',
            'job-position'    => 'Должность',
            'company'         => 'Компания',
            'status'          => 'Статус',
            'created-by'      => 'Создал',
            'updated-at'      => 'Изменено',
            'created-at'      => 'Создано',
        ],

        'groups' => [
            'job-position'    => 'Должность',
            'company'         => 'Компания',
            'department'      => 'Отдел',
            'employment-type' => 'Тип занятости',
            'created-by'      => 'Создал',
            'created-at'      => 'Создано',
            'updated-at'      => 'Изменено',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Должность восстановлена',
                    'body'  => 'Должность восстановлена.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Должность удалена',
                    'body'  => 'Должность удалена.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Должности восстановлены',
                    'body'  => 'Должности восстановлены.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Должности удалены',
                    'body'  => 'Должности удалены.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Должности удалены навсегда',
                    'body'  => 'Должности удалены навсегда.',
                ],
            ],
        ],

        'empty-state-actions' => [
            'create' => [
                'notification' => [
                    'title' => 'Должность создана',
                    'body'  => 'Должность создана.',
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
                'title' => 'Планирование персонала',

                'entries' => [
                    'expected-employees' => 'Ожидается сотрудников',
                    'current-employees'  => 'Текущих сотрудников',
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
