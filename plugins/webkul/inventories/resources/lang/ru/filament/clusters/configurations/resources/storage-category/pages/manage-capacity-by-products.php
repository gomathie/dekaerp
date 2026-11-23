<?php

return [
    'title' => 'Capacity От Товары',

    'form' => [
        'product' => 'Товар',
        'qty'     => 'Количество',
    ],

    'table' => [
        'header-actions' => [
            'create' => [
                'label' => 'Добавить товар Capacity',

                'notification' => [
                    'title' => 'Товар Capacity создано',
                    'body'  => 'товар capacity added successfully.',
                ],
            ],
        ],

        'columns' => [
            'product' => 'Товар',
            'qty'     => 'Количество',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Товар Capacity обновлено',
                    'body'  => 'товар capacity есть обновлено successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Товар Capacity удалено',
                    'body'  => 'товар capacity есть удалено successfully.',
                ],
            ],
        ],
    ],
];
