<?php

return [
    'title' => 'NDANI/NJE',

    'table' => [
        'columns' => [
            'date'                 => 'Tarehe',
            'reference'            => 'Rejea',
            'product'              => 'Bidhaa',
            'package'              => 'Kifurushi',
            'lot'                  => 'Namba za loti / mfululizo',
            'source-location'      => 'Mahali pa kuanzia',
            'destination-location' => 'Mahali pa kufikia',
            'quantity'             => 'Idadi',
            'state'                => 'Hali',
            'done-by'              => 'Imefanywa na',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Uhamisho umefutwa',
                    'body'  => 'Uhamisho umefutwa kwa mafanikio.',
                ],
            ],
        ],
    ],
];
