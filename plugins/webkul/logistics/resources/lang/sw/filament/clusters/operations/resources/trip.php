<?php

return [
    'navigation' => [
        'title' => 'Safari',
    ],

    'form' => [
        'sections' => [
            'crew'     => 'Gari na wafanyakazi',
            'schedule' => 'Ratiba',
        ],

        'fields' => [
            'company'            => 'Kampuni',
            'number'             => 'Namba ya safari',
            'number-placeholder' => 'Hutolewa kiotomatiki',
            'vehicle'            => 'Gari',
            'driver'             => 'Dereva',
            'dispatcher'         => 'Mratibu',
            'planned-start-at'   => 'Kuanza kulikopangwa',
            'planned-end-at'     => 'Kumaliza kulikopangwa',
            'odometer-start'     => 'Odomita mwanzoni',
            'odometer-end'       => 'Odomita mwishoni',
            'notes'              => 'Vidokezo',
        ],
    ],

    'table' => [
        'columns' => [
            'number'           => 'Safari',
            'state'            => 'Hali',
            'vehicle'          => 'Gari',
            'driver'           => 'Dereva',
            'shipments'        => 'Usafirishaji',
            'planned-start-at' => 'Kuanza kulikopangwa',
            'company'          => 'Kampuni',
        ],

        'filters' => [
            'state'   => 'Hali',
            'vehicle' => 'Gari',
            'driver'  => 'Dereva',
        ],
    ],

    'infolist' => [
        'summary'         => 'Safari',
        'schedule'        => 'Ratiba',
        'load'            => 'Mzigo',
        'actual-start-at' => 'Kuanza halisi',
        'actual-end-at'   => 'Kumaliza halisi',
        'capacity'        => 'Uwezo',
        'capacity-ok'     => 'Ndani ya uwezo wa gari.',
    ],

    'relations' => [
        'shipments' => [
            'title'   => 'Usafirishaji',
            'columns' => [
                'number'   => 'Usafirishaji',
                'state'    => 'Hali',
                'customer' => 'Mteja',
                'weight'   => 'Uzito (kg)',
                'volume'   => 'Ujazo (m³)',
            ],
            'actions' => [
                'attach' => [
                    'label'        => 'Ongeza usafirishaji',
                    'field'        => 'Usafirishaji uliothibitishwa',
                    'notification' => 'Usafirishaji umeongezwa kwenye safari hii.',
                ],
            ],
        ],
    ],

    'actions' => [
        'dispatchTrip' => [
            'label'        => 'Tuma',
            'heading'      => 'Tuma safari hii?',
            'notification' => 'Safari imetumwa.',
        ],
        'startTrip' => [
            'label'        => 'Anza',
            'heading'      => 'Anza safari hii?',
            'notification' => 'Safari imeanza.',
        ],
        'completeTrip' => [
            'label'        => 'Kamilisha',
            'heading'      => 'Kamilisha safari hii?',
            'notification' => 'Safari imekamilika.',
        ],
    ],
];
