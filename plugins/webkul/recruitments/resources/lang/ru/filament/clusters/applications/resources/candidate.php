<?php

return [
    'title' => 'Candidate',

    'navigation' => [
        'title' => 'Candidates',
    ],

    'global-search' => [
        'email-from' => 'Эл. почта От',
        'phone'      => 'Телефон',
        'company'    => 'Компания',
        'degree'     => 'Degree',
    ],

    'form' => [
        'sections' => [
            'basic-information' => [
                'title' => 'Basic Сведения',

                'fields' => [
                    'full-name' => 'Полный Название',
                    'email'     => 'Эл. почта Адрес',
                    'phone'     => 'Телефон Номер',
                    'linkedin'  => 'LinkedIn Профиль',
                    'contact'   => 'Контакт',
                ],
            ],

            'additional-details' => [
                'title' => 'Additional Подробности',

                'fields' => [
                    'company'           => 'Компания',
                    'degree'            => 'Degree',
                    'tags'              => 'Метки',
                    'manager'           => 'Руководитель',
                    'availability-date' => 'Availability Дата',

                    'priority-options' => [
                        'low'    => 'Низкий',
                        'medium' => 'Средний',
                        'high'   => 'Высокий',
                    ],
                ],
            ],

            'status-and-evaluation' => [
                'title' => 'Статус',

                'fields' => [
                    'active'     => 'Активен',
                    'evaluation' => 'Evaluation',
                ],
            ],

            'communication' => [
                'title' => 'Сообщения',

                'fields' => [
                    'cc-email'      => 'CC Эл. почта',
                    'email-bounced' => 'Эл. почта Bounced',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'       => 'Полный Название',
            'tags'       => 'Метки',
            'evaluation' => 'Evaluation',
        ],

        'filters' => [
            'company'      => 'Компания',
            'partner-name' => 'Контакт',
            'degree'       => 'Degree',
            'manager-name' => 'Руководитель',
        ],

        'groups' => [
            'manager-name' => 'Руководитель',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Candidate Удалено',
                    'body'  => 'candidates есть удалено successfully.',
                ],
            ],

            'empty-state-actions' => [
                'create' => [
                    'notification' => [
                        'title' => 'Candidate Создано',
                        'body'  => 'candidates есть создано successfully.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Candidates Удалено',
                    'body'  => 'candidates есть удалено successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'basic-information' => [
                'title' => 'Basic Сведения',

                'entries' => [
                    'full-name' => 'Полный Название',
                    'email'     => 'Эл. почта Адрес',
                    'phone'     => 'Телефон Номер',
                    'linkedin'  => 'LinkedIn Профиль',
                    'contact'   => 'Контакт',
                ],
            ],

            'additional-details' => [
                'title' => 'Additional Подробности',

                'entries' => [
                    'company'           => 'Компания',
                    'degree'            => 'Degree',
                    'tags'              => 'Метки',
                    'manager'           => 'Руководитель',
                    'availability-date' => 'Availability Дата',

                    'priority-options' => [
                        'low'    => 'Низкий',
                        'medium' => 'Средний',
                        'high'   => 'Высокий',
                    ],
                ],
            ],

            'status-and-evaluation' => [
                'title' => 'Статус',

                'entries' => [
                    'active'     => 'Активен',
                    'evaluation' => 'Evaluation',
                ],
            ],

            'communication' => [
                'title' => 'Сообщения',

                'entries' => [
                    'cc-email'      => 'CC Эл. почта',
                    'email-bounced' => 'Эл. почта Bounced',
                ],
            ],
        ],
    ],
];
