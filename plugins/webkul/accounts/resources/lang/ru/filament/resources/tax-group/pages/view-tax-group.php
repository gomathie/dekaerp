<?php

return [
    'header-actions' => [
        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Налог группа удалено',
                    'body'  => 'налог группа есть удалено successfully.',
                ],

                'error' => [
                    'title' => 'Налог Группа could не быть удалено',
                    'body'  => 'налог группа нельзя быть удалено потому что это currently в использовать.',
                ],
            ],
        ],
    ],
];
