<?php

return [
    'group' => [
        'label' => 'Портал Access',
    ],

    'grant' => [
        'label' => 'Grant Портал Access',

        'modal' => [
            'heading'     => 'Grant Портал Access',
            'description' => 'приглашение эл. почта быть отправлено до :email с link до задать пароль для клиент портал.',
        ],

        'notification' => [
            'email-missing' => [
                'title' => 'Эл. почта адрес обязательно',
                'body'  => 'Добавить эл. почта адрес до этот контакт до granting портал access.',
            ],

            'email-taken' => [
                'title' => 'Эл. почта адрес уже в использовать',
                'body'  => 'Another контакт с портал access уже с помощью этот эл. почта адрес.',
            ],

            'granted' => [
                'title' => 'Портал access granted',
                'body'  => 'приглашение до задать пароль есть отправлено до :email.',
            ],

            'email-failed' => [
                'title' => 'Портал access granted, но приглашение эл. почта could не быть отправлено',
            ],
        ],
    ],

    'change-password' => [
        'label' => 'Изменить Пароль',

        'modal' => [
            'heading'     => 'Изменить Портал Пароль',
            'description' => 'Задать новое клиент портал пароль для :email.',
        ],

        'form' => [
            'password' => [
                'label' => 'Новое Пароль',
            ],

            'password-confirmation' => [
                'label' => 'Подтвердить Новое Пароль',
            ],
        ],

        'notification' => [
            'changed' => [
                'title' => 'Портал пароль изменено',
                'body'  => 'контакт можно сейчас войти до клиент портал с новое пароль.',
            ],
        ],
    ],

    'password-reset' => [
        'label' => 'Send Пароль Сбросить',

        'modal' => [
            'heading'     => 'Send Пароль Сбросить',
            'description' => 'пароль сбросить link быть отправлено до :email.',
        ],

        'notification' => [
            'sent' => [
                'title' => 'Пароль сбросить link отправлено',
                'body'  => 'пароль сбросить link есть отправлено до :email.',
            ],

            'failed' => [
                'title' => 'Пароль сбросить link could не быть отправлено',
            ],
        ],
    ],

    'revoke' => [
        'label' => 'Revoke Портал Access',

        'modal' => [
            'heading'     => 'Revoke Портал Access',
            'description' => 'контакт нет longer быть able до войти до клиент портал. Любой ожидает пароль сбросить links быть invalidated.',
        ],

        'notification' => [
            'revoked' => [
                'title' => 'Портал access revoked',
                'body'  => 'контакт можно нет longer войти до клиент портал.',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'portal-access' => 'Портал Access',
        ],

        'filters' => [
            'portal-access' => 'Портал Access',
        ],
    ],

    'infolist' => [
        'section' => [
            'title' => 'Клиент Портал',
        ],

        'entries' => [
            'status' => [
                'label'   => 'Портал Access',
                'granted' => 'Granted',
                'none'    => 'Не Granted',
            ],

            'email-verified-at' => [
                'label'       => 'Эл. почта Verified В',
                'placeholder' => 'Никогда',
            ],

            'last-login-at' => [
                'label'       => 'Последний Портал Вход',
                'placeholder' => 'Никогда',
            ],
        ],
    ],
];
