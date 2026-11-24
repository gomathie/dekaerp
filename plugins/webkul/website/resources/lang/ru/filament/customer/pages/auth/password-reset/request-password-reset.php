<?php

return [
    'title'         => 'Восстановление пароля',
    'heading'       => 'Восстановление пароля',
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
        'actions' => [
            'request' => [
                'label' => 'Отправить ссылку для сброса',
            ],
        ],
    ],
    'actions' => [
        'login' => [
            'label' => 'Вернуться ко входу',
        ],
    ],
];
