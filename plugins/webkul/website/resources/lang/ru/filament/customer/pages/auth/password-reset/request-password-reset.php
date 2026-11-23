<?php

return [
    'title'         => 'Забыли пароль',
    'heading'       => 'Забыли пароль',
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
        'actions' => [
            'request' => [
                'label' => 'Send сбросить link',
            ],
        ],
    ],
    'actions' => [
        'login' => [
            'label' => 'Назад до вход',
        ],
    ],
];
