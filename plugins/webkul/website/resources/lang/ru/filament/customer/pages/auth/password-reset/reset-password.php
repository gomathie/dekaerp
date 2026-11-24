<?php

return [
    'title'         => 'Сброс пароля',
    'heading'       => 'Сброс пароля',
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
            'label'                => 'Новый пароль',
            'validation_attribute' => 'пароль',
        ],
        'password_confirmation' => [
            'label' => 'Подтвердите новый пароль',
        ],
        'actions' => [
            'reset' => [
                'label' => 'Сбросить пароль',
            ],
        ],
    ],
];
