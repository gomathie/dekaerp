<?php

return [
    'banner' => [
        'message' => 'Вы signed в как :target (:company). Impersonated от :impersonator.',
        'ends-at' => 'Ends в :time',
        'end'     => 'Конец impersonation',
    ],

    'notifications' => [
        'started' => 'Вы сейчас signed в как :name.',
        'refused' => 'Impersonation был не started',

        'ended-automatically' => [
            'expired' => 'impersonation reached его time limit и есть ended.',
            'invalid' => 'impersonation нет longer разрешено и есть ended.',
        ],

        'granted' => 'Impersonation access granted.',
        'revoked' => 'Impersonation access revoked.',
    ],

    'actions' => [
        'impersonate' => [
            'label'             => 'Impersonate',
            'modal-heading'     => 'Войти как :name',
            'modal-description' => 'Вы act как этот пользователь, с их права только, для в большинство :minutes минуты. Everything вы recorded с ваш название.',
            'submit'            => 'Начать impersonation',

            'form' => [
                'reason'   => 'Причина',
                'password' => 'Ваш пароль',
            ],
        ],

        'grant' => [
            'label'             => 'Разрешить impersonation',
            'modal-description' => 'Этот Multi-Компания Администратор быть able до impersonate tenant пользователи в их назначен компании.',
        ],

        'revoke' => [
            'label'             => 'Убрать impersonation access',
            'modal-description' => 'impersonation они running сейчас ends вкл. их далее страница load.',
        ],
    ],
];
