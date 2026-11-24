<?php

return [
    'notification' => [
        'title' => 'Заказ обновлён',
        'body'  => 'Заказ успешно обновлён.',
    ],

    'header-actions' => [
        'confirm' => [
            'label' => 'Подтвердить',
        ],

        'close' => [
            'label' => 'Закрыть',
        ],

        'cancel' => [
            'label' => 'Отменить',
        ],

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
