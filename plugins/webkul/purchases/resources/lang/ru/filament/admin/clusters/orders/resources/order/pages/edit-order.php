<?php

return [
    'notification' => [
        'title' => 'Заказ обновлено',
        'body'  => 'заказ есть обновлено successfully.',
    ],

    'header-actions' => [
        'confirm' => [
            'label' => 'Подтвердить',
        ],

        'close' => [
            'label' => 'Закрыть',
        ],

        'cancel' => [
            'label' => 'Отмена',
        ],

        'print' => [
            'label' => 'Печать',
        ],

        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Заказ удалено',
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
