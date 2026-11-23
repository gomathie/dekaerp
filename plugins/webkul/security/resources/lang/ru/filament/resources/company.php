<?php

return [
    'capacity-error' => 'Этот компания есть reached его :limit пользователь seats (:used в использовать, включая ожидает invitations). Raise seat limit до добавить больше пользователи.',
    'title'          => 'Компании',

    'navigation' => [
        'title' => 'Компании',
    ],

    'provisioning' => [
        'column' => 'Setup статус',
        'filter' => 'Setup статус',

        'statuses' => [
            'active'           => 'Активен',
            'invited'          => 'Invited, не ещё активен',
            'expired'          => 'Приглашение expired',
            'no-administrator' => 'Нет администратор',
            'closed'           => 'Закрыт',
        ],

        'resend' => [
            'label'         => 'Resend приглашение',
            'heading'       => 'Resend администратор приглашение?',
            'description'   => 'приглашение до :email отправлено снова и stays действительно для 7 больше дни.',
            'invited'       => 'Приглашение отправлено снова',
            'invite-failed' => 'приглашение could не быть отправлено снова. Это май есть accepted или revoked в meantime.',
        ],
    ],

    'global-search' => [
        'email' => 'Эл. почта',
    ],

    'pages' => [
        'create' => [
            'administrator' => [
                'invited' => [
                    'title' => 'Администратор invited',
                    'body'  => 'приглашение был отправлено до :email. Они выбрать их собственную пароль и можно затем добавить их компания’s пользователи.',
                ],

                'skipped' => [
                    'title'         => 'Нет администратор был invited',
                    'missing-email' => 'компания был создано, но это есть нет эл. почта адрес, поэтому nobody could быть invited до administer это. Добавить one и send приглашение от Пользователи страница.',
                    'missing-role'  => 'компания был создано, но Компания Администратор роль не exist в этот installation, поэтому nobody could быть invited.',

                    'seats-exhausted' => 'компания был создано, но это есть нет free пользователь seats осталось, поэтому nobody could быть invited до administer это. Raise seat limit и send приглашение от Пользователи страница.',
                    'invite-failed'   => 'компания был создано, но приглашение could не быть issued. адрес май уже belong до пользователь, или компания май не ещё быть one вы разрешено до назначить. Пригласить от Пользователи страница instead.',
                    'mail-failed'     => 'компания был создано и приглашение был issued, но эл. почта could не быть отправлено. Give этот sign-в link до :email более channel вы trust — это expires в 7 дни, и они задать их собственную пароль: :link',
                ],
            ],
        ],

        'edit' => [
            'administrator' => [
                'skipped' => [
                    'title'           => 'Нет администратор был invited',
                    'disabled'        => 'Invitations switched выкл. в пользователь настройки, поэтому :email был не invited. Задать пароль instead, или switch invitations вкл..',
                    'missing-role'    => 'Компания Администратор роль не exist в этот installation, поэтому nobody could быть invited.',
                    'seats-exhausted' => 'Этот компания есть нет free пользователь seats осталось, поэтому :email был не invited. Raise seat limit первый.',
                    'invite-failed'   => 'приглашение could не быть issued. адрес май уже belong до пользователь, или компания май не быть one вы разрешено до назначить. До сбросить existing администратор’s пароль, выбрать “Задать пароль сейчас” instead.',
                    'mail-failed'     => 'приглашение был issued, но эл. почта could не быть отправлено. Give этот sign-в link до :email более channel вы trust — это expires в 7 дни, и они задать их собственную пароль: :link',
                ],
            ],
        ],

        'administrator-account' => [
            'created' => [
                'title' => 'Администратор счёт создано',
                'body'  => ':email можно войти сейчас с пароль вы задать. Это был не emailed: give это до их yourself.',
            ],

            'password-reset' => [
                'title' => 'Администратор пароль изменено',
                'body'  => 'пароль для :email был изменено. Это был не emailed: give это до их yourself.',
            ],

            'refused' => [
                'title'             => 'администратор был не изменено',
                'missing-email'     => 'Введите администратор’s эл. почта адрес together с пароль.',
                'missing-role'      => 'Компания Администратор роль не exist в этот installation, поэтому нет счёт был создано.',
                'not-member'        => ':email belongs до пользователь кто не участник этот компания, поэтому их пароль был не изменено здесь. Manage тот пользователь от Пользователи страница.',
                'not-allowed'       => 'Вы не разрешено до manage этот компания’s администратор.',
                'seats-exhausted'   => 'Этот компания есть нет free пользователь seats осталось, поэтому нет счёт был создано для :email. Raise seat limit первый.',
            ],
        ],
    ],
];
