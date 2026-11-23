<?php

return [
    'notification' => [
        'title' => 'Пользователь обновлено',
        'body'  => 'пользователь есть обновлено successfully.',
    ],

    'header-actions' => [
        'change-password' => [
            'label' => 'Изменить Пароль',

            'notification' => [
                'title' => 'Пароль изменено',
                'body'  => 'пароль есть изменено successfully.',
            ],

            'form' => [
                'new-password'         => 'Новое Пароль',
                'confirm-new-password' => 'Подтвердить Новое Пароль',
            ],
        ],

        'delete' => [
            'notification' => [
                'title' => 'Пользователь удалено',
                'body'  => 'пользователь есть удалено successfully.',
                'error' => [
                    'title' => 'Пользователь Нельзя Быть Удалено',
                    'body'  => 'Этот по умолчанию пользователь или вы нельзя удалить yourself.',
                ],
            ],
        ],
    ],
];
