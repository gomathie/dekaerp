<?php

return [
    'administrator' => [
        'title'       => 'Администратор',
        'description' => 'person кто administers этот компания и manages его собственную пользователи.',
        'none'        => 'Нет администратор ещё для этот компания.',
        'pending'     => 'приглашение отправлено, не accepted ещё',
        'inactive'    => 'deactivated',
        'manage'      => 'Manage этот пользователь',

        'status' => [
            'active'   => 'Активен',
            'inactive' => 'Deactivated',
        ],

        'set-password' => [
            'label'       => 'Задать пароль',
            'heading'     => 'Задать новое пароль для :email',
            'description' => 'Это не emailed: give это до их yourself.',
        ],

        'add' => [
            'label'                => 'Добавить администратор',
            'heading'              => 'Добавить администратор',
            'submit'               => 'Добавить',
            'method'               => 'Как следует они получить access?',
            'invite'               => 'Send приглашение',
            'invite-description'   => 'Они получить эл. почта и выбрать их собственную пароль.',
            'password'             => 'Задать пароль сейчас',
            'password-description' => 'Для когда эл. почта не arrive или client phones в. Также resets пароль someone уже в этот компания.',
        ],

        'invitation' => [
            'waiting'          => 'Приглашение отправлено, не accepted ещё',
            'pending'          => 'Ожидает',
            'expires'          => 'Ожидает до :date',
            'resend'           => 'Resend',
            'copy-link'        => 'Копировать link',
            'copy-heading'     => 'Sign-в link для :email',
            'copy-description' => 'Anyone с этот link можно задать вверх этот администратор счёт. Send это только до их, более channel вы trust.',
            'link'             => 'Link',
        ],

        'fields' => [
            'email'                => 'Администратор эл. почта',
            'email-helper'         => 'Без пароль, приглашение отправлено до этот адрес и они выбрать их собственную пароль. Отпуск это blank до пригласить компания эл. почта.',
            'email-helper-edit'    => 'Введите адрес до пригласить новое администратор. Добавить пароль до создать их счёт straight away, или до сбросить пароль этот компания’s администратор. Отпуск blank до изменить nothing.',
            'password'             => 'Администратор пароль',
            'password-helper'      => 'Необязательно. Sets пароль сейчас instead sending приглашение, или resets это для этот компания’s администратор. Это никогда emailed: give это до их yourself.',
            'must-change-password' => 'Нужно изменить этот пароль в далее sign-в',
            'name'                 => 'Администратор название',
        ],
    ],

    'currencies' => [
        'title'       => 'Валюты',
        'description' => 'Валюты этот компания май использовать вкл. счета, счета поставщиков, коммерческие предложения и заказ на закупкуs, besides его по умолчанию валюта. Отпуск пусто до разрешить каждый активен валюта.',
        'fields'      => [
            'permitted' => 'Additional валюты',
            'available' => 'Сейчас доступно в документах: :list',
        ],
    ],

    'account' => [
        'title'       => 'Счёт тип',
        'description' => 'Whether этот компания вкл. trial или commercial счёт. Только installation staff можно выбрать этот, и только когда компания создано.',

        'fields' => [
            'type'         => 'Счёт тип',
            'commercial'   => 'Commercial',
            'test'         => 'Тест',
            'days'         => 'Trial дни',
            'days-helper'  => 'Как много дни trial lasts. Вы можно добавить больше дни later. trial не invoiced.',
        ],
    ],
];
