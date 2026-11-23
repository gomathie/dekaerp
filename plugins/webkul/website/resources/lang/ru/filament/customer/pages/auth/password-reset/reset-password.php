<?php

return [
    'title'         => 'Сбросить пароль',
    'heading'       => 'Сбросить пароль',
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
            'label'                => 'Новое пароль',
            'validation_attribute' => 'пароль',
        ],
        'password_confirmation' => [
            'label' => 'Подтвердить новое пароль',
        ],
        'actions' => [
            'reset' => [
                'label' => 'Сбросить пароль',
            ],
        ],
    ],
];
