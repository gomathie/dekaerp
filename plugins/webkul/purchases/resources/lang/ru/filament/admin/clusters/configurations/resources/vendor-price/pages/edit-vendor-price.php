<?php

return [
    'navigation' => [
        'title' => 'Изменить Поставщик Цена Список',
    ],

    'notification' => [
        'title' => 'Поставщик Цена обновлено',
        'body'  => 'поставщик цена есть обновлено successfully.',
    ],

    'header-actions' => [
        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Поставщик Цена удалено',
                    'body'  => 'поставщик цена есть удалено successfully.',
                ],

                'error' => [
                    'title' => 'Поставщик Цена could не быть удалено',
                    'body'  => 'поставщик цена нельзя быть удалено потому что это currently в использовать.',
                ],
            ],
        ],
    ],
];
