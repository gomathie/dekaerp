<?php

return [
    'header' => [
        'sub-heading' => [
            'accept-invitation' => 'Accept Приглашение',
        ],
    ],

    'title' => 'Регистрация',

    'heading' => 'Sign вверх',

    'invitation-no-longer-authorized' => 'Этот приглашение нет longer authorized. Ask администратор до send новое приглашение.',

    'actions' => [

        'login' => [
            'before' => 'или',
            'label'  => 'войти до ваш счёт',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'Эл. почта адрес',
        ],

        'name' => [
            'label' => 'Название',
        ],

        'password' => [
            'label'                => 'Пароль',
            'validation_attribute' => 'пароль',
        ],

        'password_confirmation' => [
            'label' => 'Подтвердить пароль',
        ],

        'actions' => [

            'register' => [
                'label' => 'Sign вверх',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Слишком много registration attempts',
            'body'  => 'Пожалуйста попробуйте снова в :seconds seconds.',
        ],

    ],

];
