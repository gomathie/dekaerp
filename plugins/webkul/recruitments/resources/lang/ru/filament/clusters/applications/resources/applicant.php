<?php

return [
    'title' => 'Кандидат',

    'navigation' => [
        'title' => 'Кандидаты',
    ],

    'global-search' => [
        'department' => 'Отдел',
        'work-email' => 'Work Эл. почта',
        'work-phone' => 'Work Телефон',
    ],

    'form' => [
        'sections' => [
            'general-information' => [
                'title' => 'Общее Сведения',

                'fields' => [
                    'evaluation-good'           => 'Evaluation: Good',
                    'evaluation-very-good'      => 'Evaluation: Очень Good',
                    'evaluation-very-excellent' => 'Evaluation: Очень Excellent',
                    'hired'                     => 'Hired',
                    'candidate-name'            => 'Candidate название',
                    'email'                     => 'Emails',
                    'phone'                     => 'Телефон',
                    'linkedin-profile'          => 'Linkedin Профиль',
                    'recruiter'                 => 'Recruiter',
                    'interviewer'               => 'Interviewer',
                    'tags'                      => 'Метки',
                    'notes'                     => 'Заметки',
                    'hired-date'                => 'Hired Дата',
                    'job-position'              => 'Должность Positions',
                ],
            ],

            'education-and-availability' => [
                'title' => 'Education & Availability',

                'fields' => [
                    'degree'            => 'Degree',
                    'availability-date' => 'Availability Дата',
                ],
            ],

            'department' => [
                'title' => 'Отдел',
            ],

            'salary' => [
                'title' => 'Ожидается & Proposed Salary',

                'fields' => [
                    'expected-salary'       => 'Ожидается Salary',
                    'salary-proposed-extra' => 'Другое Benefit',
                    'proposed-salary'       => 'Proposed Salary',
                    'salary-expected-extra' => 'Другое Benefit',
                ],
            ],

            'source-and-medium' => [
                'title' => 'Источник & Средний',

                'fields' => [
                    'source' => 'Источник',
                    'medium' => 'Средний',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'partner-name'       => 'Контрагент Название',
            'applied-on'         => 'Применено Вкл.',
            'job-position'       => 'Должность Должность',
            'stage'              => 'Этап',
            'candidate-name'     => 'Candidate Название',
            'evaluation'         => 'Evaluation',
            'application-status' => 'Application Статус',
            'tags'               => 'Метки',
            'refuse-reason'      => 'Отказать Причина',
            'email'              => 'Эл. почта',
            'recruiter'          => 'Recruiter',
            'interviewer'        => 'Interviewer',
            'candidate-phone'    => 'Телефон',
            'medium'             => 'Средний',
            'source'             => 'Источник',
            'salary-expected'    => 'Ожидается Salary',
            'availability-date'  => 'Availability Дата',
        ],

        'filters' => [
            'source'                  => 'Источник',
            'medium'                  => 'Средний',
            'candidate'               => 'Candidate',
            'priority'                => 'Приоритет',
            'salary-proposed-extra'   => 'Salary Proposed Extra',
            'salary-expected-extra'   => 'Salary Ожидается Extra',
            'applicant-notes'         => 'Кандидат Заметки',
            'create-date'             => 'Применено Вкл.',
            'date-closed'             => 'Hired Дата',
            'date-last-stage-updated' => 'Последний Этап Обновлено',
            'stage'                   => 'Этап',
            'job-position'            => 'Должность Должность',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Кандидат Удалено',
                    'body'  => 'кандидат был successfully удалено.',
                ],
            ],
        ],

        'groups' => [
            'stage'          => 'Этап',
            'job-position'   => 'Должность Должность',
            'candidate-name' => 'Candidate Название',
            'responsible'    => 'Responsible',
            'creation-date'  => 'Creation Дата',
            'hired-date'     => 'Hired Дата',
            'last-stage'     => 'Последний Этап',
            'refuse-reason'  => 'Отказать Причина',
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Сотрудники удалено',
                    'body'  => 'сотрудники есть удалено successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Сотрудники удалено',
                    'body'  => 'сотрудники есть удалено successfully.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Сотрудники restored',
                    'body'  => 'сотрудники есть restored successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general-information' => [
                'title' => 'Общее Сведения',

                'entries' => [
                    'evaluation-good'           => 'Evaluation: Good',
                    'evaluation-very-good'      => 'Evaluation: Очень Good',
                    'evaluation-very-excellent' => 'Evaluation: Очень Excellent',
                    'hired'                     => 'Hired',
                    'candidate-name'            => 'Candidate название',
                    'email'                     => 'Emails',
                    'phone'                     => 'Телефон',
                    'linkedin-profile'          => 'Linkedin Профиль',
                    'recruiter'                 => 'Recruiter',
                    'interviewer'               => 'Interviewer',
                    'tags'                      => 'Метки',
                    'notes'                     => 'Заметки',
                    'job-position'              => 'Должность Positions',
                ],
            ],

            'education-and-availability' => [
                'title' => 'Education & Availability',

                'entries' => [
                    'degree'            => 'Degree',
                    'availability-date' => 'Availability Дата',
                ],
            ],

            'department' => [
                'title' => 'Отдел',
            ],

            'salary' => [
                'title' => 'Ожидается & Proposed Salary',

                'entries' => [
                    'expected-salary'       => 'Ожидается Salary',
                    'salary-proposed-extra' => 'Другое Benefit',
                    'proposed-salary'       => 'Proposed Salary',
                    'salary-expected-extra' => 'Другое Benefit',
                ],
            ],

            'source-and-medium' => [
                'title' => 'Источник & Средний',

                'entries' => [
                    'source' => 'Источник',
                    'medium' => 'Средний',
                ],
            ],
        ],
    ],
];
