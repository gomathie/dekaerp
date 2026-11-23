<?php

return [
    'notification' => [
        'title' => 'Налог обновлено',
        'body'  => 'налог есть обновлено successfully.',
    ],

    'header-actions' => [
        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Налог удалено',
                    'body'  => 'налог есть удалено successfully.',
                ],

                'error' => [
                    'title' => 'Налог could не быть удалено',
                    'body'  => 'налог нельзя быть удалено потому что это currently в использовать.',
                ],

                'invalid-repartition-lines' => [
                    'title' => 'Недействительно Repartition Строки',
                ],
            ],
        ],
    ],
];
