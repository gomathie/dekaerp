<?php

return [
    'title' => 'Mienendo',

    'table' => [
        'columns' => [
            'date'                 => 'Tarehe',
            'reference'            => 'Marejeleo',
            'product'              => 'Bidhaa',
            'package'              => 'Kifurushi',
            'lot'                  => 'Makundi / Nambari za mfululizo',
            'source-location'      => 'Eneo la chanzo',
            'destination-location' => 'Eneo la kufikia',
            'quantity'             => 'Idadi',
            'unit'                 => 'Kipimo',
            'state'                => 'Hali',
            'done-by'              => 'Imefanywa na',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Mwendo umefutwa',
                    'body'  => 'Mwendo umefutwa kwa mafanikio.',
                ],
            ],
        ],
    ],
];
