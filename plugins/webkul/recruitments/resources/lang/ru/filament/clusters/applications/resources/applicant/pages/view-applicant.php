<?php

return [
    'header-actions' => [
        'delete' => [
            'notification' => [
                'title' => 'Кандидат удалено',
                'body'  => 'кандидат есть удалено successfully.',
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
            'title'        => 'Восстановить Кандидат от отказать',
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
    ],
];
