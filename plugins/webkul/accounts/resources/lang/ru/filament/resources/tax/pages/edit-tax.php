<?php

return [
    'notification' => [
        'title' => 'Налог обновлён',
        'body'  => 'Налог успешно обновлён.',
    ],

    'header-actions' => [
        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Налог удалён',
                    'body'  => 'Налог успешно удалён.',
                ],

                'error' => [
                    'title' => 'Не удалось удалить налог',
                    'body'  => 'Налог нельзя удалить: он сейчас используется.',
                ],

                'invalid-repartition-lines' => [
                    'title' => 'Неверные строки распределения',
                ],
            ],
        ],
    ],
];
