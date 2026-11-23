<?php

return [
    'tabs' => [
        'all'      => 'Все Пользователи',
        'archived' => 'В архиве Пользователи',
    ],

    'header-actions' => [
        'invitation' => 'Ожидает приглашение',
        'resend'     => [
            'title'         => 'Resend приглашение',
            'invited'       => 'Приглашение resent',
            'invite-failed' => 'Приглашение could не быть resent',
            'mail-failed'   => 'Эл. почта не удалось',
            'recovery'      => 'приглашение всё ещё активен. Share этот link более trusted channel: :link',
        ],
        'revoke' => [
            'title' => 'Revoke приглашение',
            'done'  => 'Приглашение revoked',
        ],
        'invite' => [
            'title' => 'Пригласить Пользователь',
            'modal' => [
                'submit-action-label' => 'Пригласить Пользователь',
            ],
            'form' => [
                'email' => 'Эл. почта',
            ],
            'notification' => [
                'success' => [
                    'title' => 'Пользователь invited',
                    'body'  => 'Пользователь есть invited successfully',
                ],
                'error' => [
                    'title' => 'Пользователь Приглашение Не удалось',
                    'body'  => 'система encountered unexpected ошибка пока trying до send пользователь приглашение.',
                ],

                'default-company-error' => [
                    'title' => 'По умолчанию Компания Не Задать',
                    'body'  => 'Пожалуйста задать по умолчанию компания от настройки, до inviting пользователь.',
                ],
            ],
        ],

        'create' => [
            'label' => 'Новое Пользователь',
        ],
    ],
];
