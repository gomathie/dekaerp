<?php

return [
    'title'    => 'Вход',
    'heading'  => 'Вход',
    'messages' => [
        'failed' => 'Неверные учётные данные.',
    ],
    'notifications' => [
        'throttled' => [
            'title' => 'Слишком много попыток. Повторите через :seconds с.',
            'body'  => 'Подождите :seconds с (:minutes мин) перед следующей попыткой.',
        ],
    ],
    'form' => [
        'email' => [
            'label' => 'Адрес эл. почты',
        ],
        'password' => [
            'label' => 'Пароль',
        ],
        'remember' => [
            'label' => 'Запомнить меня',
        ],
        'actions' => [
            'authenticate' => [
                'label' => 'Войти',
            ],
        ],
    ],
    'actions' => [
        'register' => [
            'before' => 'Нет аккаунта?',
            'label'  => 'Создать аккаунт',
        ],
        'request_password_reset' => [
            'label' => 'Забыли пароль?',
        ],
    ],
];
