<?php

return [
    'navigation' => [
        'label' => 'API Tokens',
    ],

    'group'      => 'Общее',
    'breadcrumb' => 'API Tokens',
    'title'      => 'API Tokens',
    'subheading' => 'Tokens client integrations использовать до authenticate против API. token carries роли и разрешено компании пользователь это belongs до.',

    'abilities' => [
        'read'  => 'Чтение только',
        'write' => 'Запись',
        'all'   => 'Полный access',
    ],

    'table' => [
        'columns' => [
            'name'      => 'Подпись',
            'user'      => 'Acts как',
            'abilities' => 'Scopes',
            'last-used' => 'Последний используется',
            'expires'   => 'Expires',
            'created'   => 'Issued',
        ],

        'never-used' => 'Никогда используется',
    ],

    'actions' => [
        'issue' => [
            'label'  => 'Issue token',
            'submit' => 'Issue token',

            'fields' => [
                'user'             => 'Acts как пользователь',
                'user-helper'      => 'token можно exactly что этот пользователь можно. Использовать dedicated пользователь за client, никогда администратор.',
                'name'             => 'Подпись',
                'name-helper'      => 'Как вы recognise этот token later, e.g. acme-склад-sync.',
                'abilities'        => 'Scopes',
                'abilities-helper' => 'Чтение только нельзя POST, PATCH или Удалить. Полный access ignores scope checks entirely.',
            ],
        ],

        'revoke' => [
            'label'       => 'Revoke',
            'heading'     => 'Revoke этот token?',
            'description' => 'integration с помощью это остановка working immediately. Этот нельзя быть undone.',
            'bulk'        => 'Revoke выбрано',
        ],
    ],

    'notifications' => [
        'issued' => [
            'title' => 'Token issued',
            'body'  => 'Копировать это сейчас - это нельзя быть shown снова.',
        ],

        'revoked'      => 'Token revoked',
        'missing-user' => 'Тот пользователь нет longer существует.',
    ],

    'plaintext' => [
        'title'       => 'Копировать этот token сейчас',
        'description' => 'Это stored только как hash, поэтому этот one time это можно быть displayed. Send это до client более channel вы trust.',
    ],

    'empty' => [
        'heading'     => 'Нет API tokens ещё',
        'description' => 'Issue one до позволить client integration authenticate против API.',
    ],
];
