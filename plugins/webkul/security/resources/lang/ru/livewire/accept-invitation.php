<?php

return [
    'header' => [
        'sub-heading' => [
            'accept-invitation' => 'Принять приглашение',
        ],
    ],

    'title' => 'Регистрация',

    'heading' => 'Зарегистрироваться',

    'invitation-no-longer-authorized' => 'Это приглашение больше не действует. Попросите администратора отправить новое.',

    'actions' => [

        'login' => [
            'before' => 'или',
            'label'  => 'войдите в свою учётную запись',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'Адрес эл. почты',
        ],

        'name' => [
            'label' => 'Имя',
        ],

        'password' => [
            'label'                => 'Пароль',
            'validation_attribute' => 'пароль',
        ],

        'password_confirmation' => [
            'label' => 'Подтвердите пароль',
        ],

        'actions' => [

            'register' => [
                'label' => 'Зарегистрироваться',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Слишком много попыток регистрации',
            'body'  => 'Повторите попытку через :seconds с.',
        ],

    ],

];
