<?php

return [
    'title'    => 'Вход',
    'heading'  => 'Вход',
    'messages' => [
        'failed' => 'Эти credentials не совпадение наш записи.',
    ],
    'notifications' => [
        'throttled' => [
            'title' => 'Слишком много attempts. Попробуйте снова в :seconds seconds.',
            'body'  => 'Пожалуйста wait :seconds seconds (:minutes минуты) до trying снова.',
        ],
    ],
    'form' => [
        'email' => [
            'label' => 'Эл. почта адрес',
        ],
        'password' => [
            'label' => 'Пароль',
        ],
        'remember' => [
            'label' => 'Remember мне',
        ],
        'actions' => [
            'authenticate' => [
                'label' => 'Войти',
            ],
        ],
    ],
    'actions' => [
        'register' => [
            'before' => 'Don\'t есть счёт?',
            'label'  => 'Создать счёт',
        ],
        'request_password_reset' => [
            'label' => 'Забыли пароль?',
        ],
    ],
];
