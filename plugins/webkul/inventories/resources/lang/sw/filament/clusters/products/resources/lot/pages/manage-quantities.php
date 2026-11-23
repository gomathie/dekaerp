<?php

return [
    'title' => 'Maeneo',

    'table' => [
        'columns' => [
            'product'          => 'Bidhaa',
            'location'         => 'Mahali',
            'storage-category' => 'Storage Kategoria',
            'quantity'         => 'Kiasi',
            'package'          => 'Kifurushi',
            'on-hand'          => 'Washa Mkono Kiasi',
            'unit'             => 'Uniti',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Kiasi imefutwa',
                    'body'  => 'kiasi ina imefutwa successfully.',
                ],
            ],
        ],
    ],
];
