<?php

return [
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
];
