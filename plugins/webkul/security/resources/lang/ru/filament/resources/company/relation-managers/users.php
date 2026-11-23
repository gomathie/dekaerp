<?php

return [
    'title' => 'Пользователи и logins',

    'table' => [
        'columns' => [
            'name'                 => 'Название',
            'login'                => 'Вход (эл. почта)',
            'company-admin'        => 'Компания Администратор',
            'active'               => 'Активен',
            'must-change-password' => 'Нужно изменить пароль',
        ],

        'empty-state' => [
            'heading'     => 'Нет пользователи ещё',
            'description' => 'Пригласить или создать администратор в Администратор раздел выше.',
        ],

        'actions' => [
            'copy-invitation-link' => [
                'label'      => 'Копировать приглашение link',
                'invitation' => 'Ожидает приглашение',
                'link'       => 'Приглашение link',
            ],
            'show-password' => [
                'label'             => 'Показать пароль',
                'modal-heading'     => 'Показать пароль для :email',
                'modal-description' => 'Введите ваш собственную пароль до продолжить. Этот просмотр recorded в audit log.',
                'submit'            => 'Показать пароль',
                'shown'             => 'Пароль для :email',

                'form' => [
                    'password' => 'Ваш пароль',
                ],
            ],
            'set-password' => [
                'label'             => 'Задать пароль',
                'modal-heading'     => 'Задать пароль для :email',
                'modal-description' => 'Existing passwords нельзя быть shown, потому что они stored encrypted one way. Задать новое one и give это до пользователь yourself: это не emailed.',

                'form' => [
                    'password'             => 'Новое пароль',
                    'must-change-password' => 'Нужно изменить этот пароль в далее sign-в',
                ],
            ],
        ],
    ],
];
