<?php

return [
    'title' => 'Соискатель',

    'navigation' => [
        'title' => 'Соискатели',
    ],

    'global-search' => [
        'department' => 'Отдел',
        'work-email' => 'Рабочая эл. почта',
        'work-phone' => 'Рабочий телефон',
    ],

    'form' => [
        'sections' => [
            'general-information' => [
                'title' => 'Общие сведения',

                'fields' => [
                    'evaluation-good'           => 'Оценка: хорошо',
                    'evaluation-very-good'      => 'Оценка: очень хорошо',
                    'evaluation-very-excellent' => 'Оценка: отлично',
                    'hired'                     => 'Нанят',
                    'candidate-name'            => 'Имя кандидата',
                    'email'                     => 'Эл. почта',
                    'phone'                     => 'Телефон',
                    'linkedin-profile'          => 'Профиль LinkedIn',
                    'recruiter'                 => 'Рекрутер',
                    'interviewer'               => 'Интервьюер',
                    'tags'                      => 'Теги',
                    'notes'                     => 'Примечания',
                    'hired-date'                => 'Дата найма',
                    'job-position'              => 'Должности',
                ],
            ],

            'education-and-availability' => [
                'title' => 'Образование и доступность',

                'fields' => [
                    'degree'            => 'Степень',
                    'availability-date' => 'Дата готовности',
                ],
            ],

            'department' => [
                'title' => 'Отдел',
            ],

            'salary' => [
                'title' => 'Ожидаемая и предлагаемая зарплата',

                'fields' => [
                    'expected-salary'       => 'Ожидаемая зарплата',
                    'salary-proposed-extra' => 'Прочие льготы',
                    'proposed-salary'       => 'Предлагаемая зарплата',
                    'salary-expected-extra' => 'Прочие льготы',
                ],
            ],

            'source-and-medium' => [
                'title' => 'Источник и канал',

                'fields' => [
                    'source' => 'Источник',
                    'medium' => 'Канал',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'partner-name'       => 'Имя контакта',
            'applied-on'         => 'Дата подачи',
            'job-position'       => 'Должность',
            'stage'              => 'Этап',
            'candidate-name'     => 'Имя кандидата',
            'evaluation'         => 'Оценка',
            'application-status' => 'Статус заявки',
            'tags'               => 'Теги',
            'refuse-reason'      => 'Причина отказа',
            'email'              => 'Эл. почта',
            'recruiter'          => 'Рекрутер',
            'interviewer'        => 'Интервьюер',
            'candidate-phone'    => 'Телефон',
            'medium'             => 'Канал',
            'source'             => 'Источник',
            'salary-expected'    => 'Ожидаемая зарплата',
            'availability-date'  => 'Дата готовности',
        ],

        'filters' => [
            'source'                  => 'Источник',
            'medium'                  => 'Канал',
            'candidate'               => 'Кандидат',
            'priority'                => 'Приоритет',
            'salary-proposed-extra'   => 'Доп. льготы (предлагаемые)',
            'salary-expected-extra'   => 'Доп. льготы (ожидаемые)',
            'applicant-notes'         => 'Примечания о соискателе',
            'create-date'             => 'Дата подачи',
            'date-closed'             => 'Дата найма',
            'date-last-stage-updated' => 'Последняя смена этапа',
            'stage'                   => 'Этап',
            'job-position'            => 'Должность',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Соискатель удалён',
                    'body'  => 'Соискатель успешно удалён.',
                ],
            ],
        ],

        'groups' => [
            'stage'          => 'Этап',
            'job-position'   => 'Должность',
            'candidate-name' => 'Имя кандидата',
            'responsible'    => 'Ответственный',
            'creation-date'  => 'Дата создания',
            'hired-date'     => 'Дата найма',
            'last-stage'     => 'Последний этап',
            'refuse-reason'  => 'Причина отказа',
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Сотрудники удалены',
                    'body'  => 'Сотрудники успешно удалены.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Сотрудники удалены',
                    'body'  => 'Сотрудники успешно удалены.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Сотрудники восстановлены',
                    'body'  => 'Сотрудники успешно восстановлены.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general-information' => [
                'title' => 'Общие сведения',

                'entries' => [
                    'evaluation-good'           => 'Оценка: хорошо',
                    'evaluation-very-good'      => 'Оценка: очень хорошо',
                    'evaluation-very-excellent' => 'Оценка: отлично',
                    'hired'                     => 'Нанят',
                    'candidate-name'            => 'Имя кандидата',
                    'email'                     => 'Эл. почта',
                    'phone'                     => 'Телефон',
                    'linkedin-profile'          => 'Профиль LinkedIn',
                    'recruiter'                 => 'Рекрутер',
                    'interviewer'               => 'Интервьюер',
                    'tags'                      => 'Теги',
                    'notes'                     => 'Примечания',
                    'job-position'              => 'Должности',
                ],
            ],

            'education-and-availability' => [
                'title' => 'Образование и доступность',

                'entries' => [
                    'degree'            => 'Степень',
                    'availability-date' => 'Дата готовности',
                ],
            ],

            'department' => [
                'title' => 'Отдел',
            ],

            'salary' => [
                'title' => 'Ожидаемая и предлагаемая зарплата',

                'entries' => [
                    'expected-salary'       => 'Ожидаемая зарплата',
                    'salary-proposed-extra' => 'Прочие льготы',
                    'proposed-salary'       => 'Предлагаемая зарплата',
                    'salary-expected-extra' => 'Прочие льготы',
                ],
            ],

            'source-and-medium' => [
                'title' => 'Источник и канал',

                'entries' => [
                    'source' => 'Источник',
                    'medium' => 'Канал',
                ],
            ],
        ],
    ],
];
