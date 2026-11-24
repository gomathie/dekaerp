<?php

return [
    'form' => [
        'fields' => [
            'tax-source'      => 'Исходный налог',
            'tax-destination' => 'Целевой налог',
        ],
    ],

    'table' => [
        'columns' => [
            'tax-source'      => 'Исходный налог',
            'tax-destination' => 'Целевой налог',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Срок платежа обновлён',
                    'body'  => 'Срок платежа успешно обновлён.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Срок платежа удалён',
                    'body'  => 'Срок платежа успешно удалён.',
                ],
            ],
        ],

        'header-actions' => [
            'create' => [
                'notification' => [
                    'title' => 'Срок платежа создан',
                    'body'  => 'Срок платежа успешно создан.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'tax-source'      => 'Исходный налог',
            'tax-destination' => 'Целевой налог',
        ],
    ],
];
