<?php

return [
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
            ],
        ],
    ],
];
