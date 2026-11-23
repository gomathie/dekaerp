<?php

return [
    'notification' => [
        'title' => 'Dropship обновлено',
        'body'  => 'dropship есть обновлено successfully.',
    ],

    'header-actions' => [
        'print' => [
            'label' => 'Печать',
        ],

        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Dropship удалено',
                    'body'  => 'dropship есть удалено successfully.',
                ],

                'error' => [
                    'title' => 'Dropship could не быть удалено',
                    'body'  => 'dropship нельзя быть удалено потому что это currently в использовать.',
                ],
            ],
        ],
    ],
];
