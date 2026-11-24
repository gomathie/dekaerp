<?php

return [
    'create-employee' => 'Создать сотрудника',
    'goto-employee'   => 'Перейти к сотруднику',

    'notification' => [
        'title' => 'Соискатель обновлён',
        'body'  => 'Соискатель успешно обновлён.',
    ],

    'header-actions' => [
        'delete' => [
            'notification' => [
                'title' => 'Соискатель удалён',
                'body'  => 'Соискатель успешно удалён.',
            ],
        ],
        'force-delete' => [
            'notification' => [
                'title' => 'Соискатель удалён',
                'body'  => 'Соискатель удалён безвозвратно.',
            ],
        ],

        'restore' => [
            'notification' => [
                'title' => 'Соискатель восстановлен',
                'body'  => 'Соискатель успешно восстановлен.',
            ],
        ],

        'refuse' => [
            'title'        => 'Причина отказа',
            'form'         => [
                'fields' => [
                    'notify'   => 'Уведомить',
                    'email-to' => 'Кому',
                ],
            ],
            'notification' => [
                'title' => 'Соискателю отказано',
                'body'  => 'Соискателю успешно отказано.',
            ],
        ],

        'reopen' => [
            'title'        => 'Вернуть соискателя',
            'notification' => [
                'title' => 'Соискатель возвращён',
                'body'  => 'Соискатель успешно возвращён в работу.',
            ],
        ],

        'state' => [
            'notification' => [
                'title' => 'Статус соискателя обновлён',
                'body'  => 'Статус соискателя успешно обновлён.',
            ],
        ],
    ],

    'mail' => [
        'application-refused' => [
            'subject' => 'Ваша заявка на вакансию: :application',
        ],

        'application-confirm' => [
            'subject' => 'Ваша заявка на вакансию: :job_position',
        ],
        'interviewer-assigned' => [
            'subject' => 'Вам назначен соискатель :applicant.',
        ],
    ],
];
