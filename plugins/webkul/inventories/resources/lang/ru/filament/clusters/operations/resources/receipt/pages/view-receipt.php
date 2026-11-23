<?php

return [
    'header-actions' => [
        'print' => [
            'label' => 'Печать',
        ],

        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Приход Удалено',
                    'body'  => 'приход есть удалено successfully.',
                ],

                'error' => [
                    'title' => 'Приход could не быть удалено',
                    'body'  => 'Приход нельзя быть удалено потому что это currently в использовать.',
                ],
            ],
        ],
    ],
];
