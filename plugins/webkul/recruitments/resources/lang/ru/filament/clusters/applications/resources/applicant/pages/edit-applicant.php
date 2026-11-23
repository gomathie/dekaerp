<?php

return [
    'create-employee' => 'Создать Сотрудник',
    'goto-employee'   => 'Go до Сотрудник',

    'notification' => [
        'title' => 'Кандидат обновлено',
        'body'  => 'кандидат есть обновлено successfully.',
    ],

    'header-actions' => [
        'delete' => [
            'notification' => [
                'title' => 'Кандидат удалено',
                'body'  => 'кандидат есть удалено successfully.',
            ],
        ],
        'force-delete' => [
            'notification' => [
                'title' => 'Кандидат удалено',
                'body'  => 'кандидат есть force удалено successfully.',
            ],
        ],

        'restore' => [
            'notification' => [
                'title' => 'Кандидат restored',
                'body'  => 'кандидат есть restored successfully.',
            ],
        ],

        'refuse' => [
            'title'        => 'Отказать Причина',
            'form'         => [
                'fields' => [
                    'notify'   => 'Notify',
                    'email-to' => 'Эл. почта До',
                ],
            ],
            'notification' => [
                'title' => 'Кандидат отказано',
                'body'  => 'кандидат есть отказано successfully.',
            ],
        ],

        'reopen' => [
            'title'        => 'Reopen Кандидат',
            'notification' => [
                'title' => 'Кандидат reopened',
                'body'  => 'кандидат есть reopened successfully.',
            ],
        ],

        'state' => [
            'notification' => [
                'title' => 'Кандидат состояние обновлено',
                'body'  => 'кандидат состояние есть обновлено successfully.',
            ],
        ],
    ],

    'mail' => [
        'application-refused' => [
            'subject' => 'Ваш Должность Application: :application',
        ],

        'application-confirm' => [
            'subject' => 'Ваш Должность Application: :job_position',
        ],
        'interviewer-assigned' => [
            'subject' => 'Вы есть назначен до Кандидат :applicant.',
        ],
    ],
];
