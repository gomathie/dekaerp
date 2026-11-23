<?php

return [
    'breadcrumb' => 'Manage Пользователи',
    'title'      => 'Manage Пользователи',
    'group'      => 'Общее',

    'navigation' => [
        'label' => 'Manage Пользователи',
    ],

    'form' => [
        'enable-user-invitation' => [
            'label'       => 'Включить Пользователь Приглашение',
            'helper-text' => 'Разрешить пользователи до пригласить другое пользователи до application.',
        ],

        'enable-reset-password' => [
            'label'       => 'Включить Сбросить пароль',
            'helper-text' => 'Разрешить пользователи до сбросить их пароль.',
        ],

        'default-role' => [
            'label'       => 'По умолчанию Роль',
            'helper-text' => 'по умолчанию роль назначен до новое пользователи.',
        ],
        'company-admin-business-role' => [
            'label'       => 'Компания Администратор Business Роль',
            'helper-text' => 'Given до каждый новое компания администратор alongside Компания Администратор, поэтому они можно run их компания. Platform роли не offered.',
        ],

        'default-company' => [
            'label'       => 'По умолчанию Компания',
            'helper-text' => 'по умолчанию компания назначен до новое пользователи.',
        ],
    ],
];
