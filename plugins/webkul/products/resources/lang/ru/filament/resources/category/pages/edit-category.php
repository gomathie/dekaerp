<?php

return [
    'notification' => [
        'title' => 'Категория обновлено',
        'body'  => 'категория есть обновлено successfully.',
    ],

    'header-actions' => [
        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Категория удалено',
                    'body'  => 'категория есть удалено successfully.',
                ],

                'error' => [
                    'title' => 'Категория could не быть удалено',
                    'body'  => 'категория нельзя быть удалено потому что это currently в использовать.',
                ],
            ],
        ],
    ],

    'save' => [
        'notification' => [
            'error' => [
                'title' => 'Категория обновлено не удалось',
            ],
        ],
    ],
];
