<?php

return [
    'title'                   => 'Профиль',
    'heading'                 => 'Профиль',
    'subheading'              => 'Manage ваш счёт настройки и preferences.',
    'information_section'     => 'Профиль Сведения',
    'information_description' => "Обновить ваш account's профиль сведения и эл. почта адрес.",

    'notification' => [
        'success' => [
            'title' => 'Профиль Обновлено',
            'body'  => 'Ваш профиль есть обновлено successfully.',
        ],

        'error' => [
            'title' => 'Профиль Обновить Не удалось',
            'body'  => 'был ошибка updating ваш профиль.',
        ],

        'validation-error' => [
            'title' => 'Validation Ошибка',
        ],
    ],

    'actions' => [
        'save' => 'Сохранить Changes',
    ],

    'fields' => [
        'avatar'          => 'Профиль Photo',
        'name'            => 'Название',
        'email'           => 'Эл. почта',
        'language'        => 'Preferred Язык',
        'language_helper' => 'администратор interface быть shown в этот язык.',
    ],

    'password' => [
        'section'     => 'Обновить Пароль',
        'description' => 'Ensure ваш счёт с помощью long, random пароль до stay secure.',
        'current'     => 'Текущий Пароль',
        'new'         => 'Новое Пароль',
        'confirm'     => 'Подтвердить Пароль',
        'helper'      => 'Нужно быть в наименее 8 characters long.',

        'errors' => [
            'current-required'  => 'Текущий пароль обязательно.',
            'current-incorrect' => 'текущий пароль incorrect. Пожалуйста попробуйте снова.',
            'same-as-current'   => 'новое пароль нужно быть different от ваш текущий пароль.',
        ],

        'current-helper' => 'Введите ваш текущий пароль до verify ваш identity.',

        'must-change' => [
            'title' => 'Выбрать ваш собственную пароль',
            'body'  => 'Ваш администратор задать этот пароль для вы. Открыт "Обновить Пароль" и выбрать ваш собственную до продолжить.',
        ],

        'signed-out' => 'Ваш пароль есть обновлено. Войти снова с ваш новое пароль.',

        'notification' => [
            'success' => [
                'title' => 'Пароль Обновлено',
                'body'  => 'Ваш пароль есть обновлено successfully.',
            ],

            'error' => [
                'title' => 'Пароль Обновить Не удалось',
                'body'  => 'был ошибка updating ваш пароль.',
            ],
        ],
    ],
];
