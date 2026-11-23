<?php

return [
    'header-actions' => [
        'print' => [
            'label' => 'Печать',
        ],

        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Заказ Удалено',
                    'body'  => 'заказ есть удалено successfully.',
                ],

                'error' => [
                    'title' => 'Заказ could не быть удалено',
                    'body'  => 'заказ нельзя быть удалено потому что это currently в использовать.',
                ],
            ],
        ],
    ],
];
