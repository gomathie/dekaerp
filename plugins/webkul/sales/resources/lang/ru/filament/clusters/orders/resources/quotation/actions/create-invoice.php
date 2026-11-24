<?php

return [
    'title' => 'Создать счёт',

    'modal' => [
        'heading' => 'Создать счёт',
    ],

    'notification' => [
        'invoice-created' => [
            'title' => 'Счёт создан',
            'body'  => 'Счёт успешно создан.',
        ],

        'no-invoiceable-lines' => [
            'title' => 'Нет строк для выставления',
            'body'  => 'Нет строк для выставления счёта. Убедитесь, что количество получено.',
        ],
    ],

    'form' => [
        'fields' => [
            'create-invoice' => 'Создать счёт',
        ],
    ],
];
