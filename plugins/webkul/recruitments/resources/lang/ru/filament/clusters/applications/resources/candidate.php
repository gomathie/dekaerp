<?php

return [
    'title' => 'Кандидат',

    'navigation' => [
        'title' => 'Кандидаты',
    ],

    'global-search' => [
        'email-from' => 'Отправитель',
        'phone'      => 'Телефон',
        'company'    => 'Компания',
        'degree'     => 'Степень',
    ],

    'form' => [
        'sections' => [
            'basic-information' => [
                'title' => 'Основные сведения',

                'fields' => [
                    'full-name' => 'Полное имя',
                    'email'     => 'Адрес эл. почты',
                    'phone'     => 'Номер телефона',
                    'linkedin'  => 'Профиль LinkedIn',
                    'contact'   => 'Контакт',
                ],
            ],

            'additional-details' => [
                'title' => 'Дополнительные сведения',

                'fields' => [
                    'company'           => 'Компания',
                    'degree'            => 'Степень',
                    'tags'              => 'Теги',
                    'manager'           => 'Руководитель',
                    'availability-date' => 'Дата готовности',

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
                    'evaluation' => 'Оценка',
                ],
            ],

            'communication' => [
                'title' => 'Связь',

                'fields' => [
                    'cc-email'      => 'Копия на эл. почту',
                    'email-bounced' => 'Письмо не доставлено',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'       => 'Полное имя',
            'tags'       => 'Теги',
            'evaluation' => 'Оценка',
        ],

        'filters' => [
            'company'      => 'Компания',
            'partner-name' => 'Контакт',
            'degree'       => 'Степень',
            'manager-name' => 'Руководитель',
        ],

        'groups' => [
            'manager-name' => 'Руководитель',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Кандидат удалён',
                    'body'  => 'Кандидаты успешно удалены.',
                ],
            ],

            'empty-state-actions' => [
                'create' => [
                    'notification' => [
                        'title' => 'Кандидат создан',
                        'body'  => 'Кандидат успешно создан.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Кандидаты удалены',
                    'body'  => 'Кандидаты успешно удалены.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'basic-information' => [
                'title' => 'Основные сведения',

                'entries' => [
                    'full-name' => 'Полное имя',
                    'email'     => 'Адрес эл. почты',
                    'phone'     => 'Номер телефона',
                    'linkedin'  => 'Профиль LinkedIn',
                    'contact'   => 'Контакт',
                ],
            ],

            'additional-details' => [
                'title' => 'Дополнительные сведения',

                'entries' => [
                    'company'           => 'Компания',
                    'degree'            => 'Степень',
                    'tags'              => 'Теги',
                    'manager'           => 'Руководитель',
                    'availability-date' => 'Дата готовности',

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
                    'evaluation' => 'Оценка',
                ],
            ],

            'communication' => [
                'title' => 'Связь',

                'entries' => [
                    'cc-email'      => 'Копия на эл. почту',
                    'email-bounced' => 'Письмо не доставлено',
                ],
            ],
        ],
    ],
];
