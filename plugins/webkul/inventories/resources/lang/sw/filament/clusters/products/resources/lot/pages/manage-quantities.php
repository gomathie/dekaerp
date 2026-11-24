<?php

return [
    'title' => 'Maeneo',

    'table' => [
        'columns' => [
            'product'          => 'Bidhaa',
            'location'         => 'Eneo',
            'storage-category' => 'Kategoria ya uhifadhi',
            'quantity'         => 'Idadi',
            'package'          => 'Kifurushi',
            'on-hand'          => 'Idadi iliyopo',
            'unit'             => 'Kipimo',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Idadi imefutwa',
                    'body'  => 'Idadi imefutwa kwa mafanikio.',
                ],
            ],
        ],
    ],
];
