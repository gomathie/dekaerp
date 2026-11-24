<?php

return [
    'title' => 'NDANI/NJE',

    'table' => [
        'columns' => [
            'date'                 => 'Tarehe',
            'reference'            => 'Kumbukumbu',
            'product'              => 'Bidhaa',
            'package'              => 'Kifurushi',
            'lot'                  => 'Kundi / Nambari za mfululizo',
            'source-location'      => 'Mahali chanzo',
            'destination-location' => 'Mahali pa kufikia',
            'quantity'             => 'Idadi',
            'state'                => 'Hali',
            'done-by'              => 'Imefanywa na',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Mwendo umefutwa',
                    'body'  => 'Mwendo umefutwa.',
                ],
            ],
        ],
    ],
];
