<?php

return [
    'navigation' => [
        'title' => 'Safari',
    ],

    'form' => [
        'sections' => [
            'crew'     => 'Gari na crew',
            'schedule' => 'Ratiba',
        ],

        'fields' => [
            'company'            => 'Kampuni',
            'number'             => 'Safari nambari',
            'number-placeholder' => 'Amekabidhiwa kiotomatiki',
            'vehicle'            => 'Gari',
            'driver'             => 'Dereva',
            'dispatcher'         => 'Dispatcher',
            'planned-start-at'   => 'Imepangwa anza',
            'planned-end-at'     => 'Imepangwa mwisho',
            'odometer-start'     => 'Odometer anza',
            'odometer-end'       => 'Odometer mwisho',
            'notes'              => 'Maelezo',
        ],
    ],

    'table' => [
        'columns' => [
            'number'           => 'Safari',
            'state'            => 'Hali',
            'vehicle'          => 'Gari',
            'driver'           => 'Dereva',
            'shipments'        => 'Shehena',
            'planned-start-at' => 'Imepangwa anza',
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
        'load'            => 'Load',
        'actual-start-at' => 'Halisi anza',
        'actual-end-at'   => 'Halisi mwisho',
        'capacity'        => 'Capacity',
        'capacity-ok'     => 'Ndani ya gari’s capacity.',
    ],

    'relations' => [
        'shipments' => [
            'title'   => 'Shehena',
            'columns' => [
                'number'   => 'Shehena',
                'state'    => 'Hali',
                'customer' => 'Mteja',
                'weight'   => 'Uzito (kg)',
                'volume'   => 'Ujazo (m³)',
            ],
            'actions' => [
                'attach' => [
                    'label'        => 'Ongeza shehena',
                    'field'        => 'Imethibitishwa shehena',
                    'notification' => 'Shehena added hadi hii safari.',
                ],
            ],
        ],
    ],

    'actions' => [
        'dispatchTrip' => [
            'label'        => 'Dispatch',
            'heading'      => 'Dispatch hii safari?',
            'notification' => 'Safari dispatched.',
        ],
        'startTrip' => [
            'label'        => 'Anza',
            'heading'      => 'Anza hii safari?',
            'notification' => 'Safari started.',
        ],
        'completeTrip' => [
            'label'        => 'Kamilisha',
            'heading'      => 'Kamilisha hii safari?',
            'notification' => 'Safari imekamilika.',
        ],
    ],
];
