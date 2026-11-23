<?php

return [
    'notification' => [
        'title'              => 'Отгул и отпуск обновлено',
        'body'               => 'отгул и отпуск есть обновлено successfully.',
        'action_not_allowed' => [
            'title' => 'Действие Не Разрешено',
            'body'  => 'Вы нельзя modify этот отпуск request как это в заблокировано состояние.',
        ],
        'overlap' => [
            'title' => 'Overlapping Отпуск Request',
            'body'  => 'выбрано отпуск dates overlap с existing request. Пожалуйста выбрать different dates.',
        ],
    ],

    'header-actions' => [
        'delete' => [
            'notification' => [
                'title' => 'Отгул и отпуск удалено',
                'body'  => 'отгул и отпуск есть удалено successfully.',
            ],
        ],
    ],
];
