<?php

return [
    'title' => 'Места',

    'table' => [
        'columns' => [
            'product'          => 'Товар',
            'location'         => 'Место',
            'storage-category' => 'Storage Категория',
            'quantity'         => 'Количество',
            'package'          => 'Упаковка',
            'on-hand'          => 'Вкл. Вручную Количество',
            'unit'             => 'Единица',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Количество удалено',
                    'body'  => 'количество есть удалено successfully.',
                ],
            ],
        ],
    ],
];
