<?php

return [
    'header-actions' => [
        'print' => [
            'label' => 'Печать',
        ],

        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Заказ удалён',
                    'body'  => 'Заказ успешно удалён.',
                ],

                'error' => [
                    'title' => 'Не удалось удалить заказ',
                    'body'  => 'Заказ нельзя удалить, так как он используется.',
                ],
            ],
        ],
    ],
];
