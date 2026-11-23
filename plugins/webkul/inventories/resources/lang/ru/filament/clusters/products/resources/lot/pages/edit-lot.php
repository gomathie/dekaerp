<?php

return [
    'notification' => [
        'title' => 'Партия обновлено',
        'body'  => 'партия есть обновлено successfully.',
    ],

    'header-actions' => [
        'print' => [
            'label' => 'Печать',
        ],

        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Партия удалено',
                    'body'  => 'партия есть удалено successfully.',
                ],

                'error' => [
                    'title' => 'Партия could не быть удалено',
                    'body'  => 'партия нельзя быть удалено потому что это currently в использовать.',
                ],
            ],
        ],
    ],
];
