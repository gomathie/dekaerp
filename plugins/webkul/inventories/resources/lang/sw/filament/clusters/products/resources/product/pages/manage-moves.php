<?php

return [
    'title' => 'Katika/Nje',

    'tabs' => [
        'todo'     => 'Hadi',
        'done'     => 'Imekamilika',
        'incoming' => 'Incoming',
        'outgoing' => 'Outgoing',
        'internal' => 'Ndani',
    ],

    'table' => [
        'columns' => [
            'date'                 => 'Tarehe',
            'reference'            => 'Kumbukumbu',
            'product'              => 'Bidhaa',
            'package'              => 'Kifurushi',
            'lot'                  => 'Kundi / Nambari ya mfululizo Numbers',
            'source-location'      => 'Chanzo Mahali',
            'destination-location' => 'Unakoenda Mahali',
            'quantity'             => 'Kiasi',
            'unit'                 => 'Uniti',
            'state'                => 'Hali',
            'done-by'              => 'Imekamilika Na',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Hamisha imefutwa',
                    'body'  => 'hamisha ina imefutwa successfully.',
                ],
            ],
        ],
    ],
];
