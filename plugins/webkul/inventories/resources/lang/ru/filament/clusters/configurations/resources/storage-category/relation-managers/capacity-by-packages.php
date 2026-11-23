<?php

return [
    'title' => 'Capacity От Упаковки',

    'form' => [
        'package-type' => 'Упаковка Тип',
        'qty'          => 'Количество',
    ],

    'table' => [
        'header-actions' => [
            'create' => [
                'label' => 'Добавить Упаковка Тип Capacity',

                'notification' => [
                    'title' => 'Упаковка Тип Capacity создано',
                    'body'  => 'упаковка тип capacity added successfully.',
                ],
            ],
        ],

        'columns' => [
            'package-type' => 'Упаковка Тип',
            'qty'          => 'Количество',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Упаковка Тип Capacity обновлено',
                    'body'  => 'упаковка тип capacity есть обновлено successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Упаковка Тип Capacity удалено',
                    'body'  => 'упаковка тип capacity есть удалено successfully.',
                ],
            ],
        ],
    ],
];
