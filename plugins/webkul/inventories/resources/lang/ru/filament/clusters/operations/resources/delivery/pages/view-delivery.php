<?php

return [
    'header-actions' => [
        'print' => [
            'label' => 'Печать',
        ],

        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Доставка Удалено',
                    'body'  => 'доставка есть удалено successfully.',
                ],

                'error' => [
                    'title' => 'Доставка could не быть удалено',
                    'body'  => 'доставка нельзя быть удалено потому что это currently в использовать.',
                ],
            ],
        ],
    ],
];
