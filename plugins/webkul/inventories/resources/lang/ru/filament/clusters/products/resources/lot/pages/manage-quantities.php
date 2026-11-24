<?php

return [
    'title' => 'Местоположения',

    'table' => [
        'columns' => [
            'product'          => 'Товар',
            'location'         => 'Местоположение',
            'storage-category' => 'Категория хранения',
            'quantity'         => 'Количество',
            'package'          => 'Упаковка',
            'on-hand'          => 'Количество в наличии',
            'unit'             => 'Единица',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Количество удалено',
                    'body'  => 'Количество успешно удалено.',
                ],
            ],
        ],
    ],
];
