<?php

return [
    'navigation' => [
        'title' => 'Trips',
    ],

    'form' => [
        'sections' => [
            'crew'     => 'Vehicle and crew',
            'schedule' => 'Schedule',
        ],

        'fields' => [
            'company'            => 'Company',
            'number'             => 'Trip number',
            'number-placeholder' => 'Assigned automatically',
            'vehicle'            => 'Vehicle',
            'driver'             => 'Driver',
            'dispatcher'         => 'Dispatcher',
            'planned-start-at'   => 'Planned start',
            'planned-end-at'     => 'Planned end',
            'odometer-start'     => 'Odometer start',
            'odometer-end'       => 'Odometer end',
            'notes'              => 'Notes',
        ],
    ],

    'table' => [
        'columns' => [
            'number'           => 'Trip',
            'state'            => 'Status',
            'vehicle'          => 'Vehicle',
            'driver'           => 'Driver',
            'shipments'        => 'Shipments',
            'planned-start-at' => 'Planned start',
            'company'          => 'Company',
        ],

        'filters' => [
            'state'   => 'Status',
            'vehicle' => 'Vehicle',
            'driver'  => 'Driver',
        ],
    ],

    'infolist' => [
        'summary'         => 'Trip',
        'schedule'        => 'Schedule',
        'load'            => 'Load',
        'actual-start-at' => 'Actual start',
        'actual-end-at'   => 'Actual end',
        'capacity'        => 'Capacity',
        'capacity-ok'     => 'Within the vehicle’s capacity.',
    ],

    'relations' => [
        'shipments' => [
            'title'   => 'Shipments',
            'columns' => [
                'number'   => 'Shipment',
                'state'    => 'Status',
                'customer' => 'Customer',
                'weight'   => 'Weight (kg)',
                'volume'   => 'Volume (m³)',
            ],
            'actions' => [
                'attach' => [
                    'label'        => 'Add shipment',
                    'field'        => 'Confirmed shipment',
                    'notification' => 'Shipment added to this trip.',
                ],
            ],
        ],
    ],

    'actions' => [
        'dispatchTrip' => [
            'label'        => 'Dispatch',
            'heading'      => 'Dispatch this trip?',
            'notification' => 'Trip dispatched.',
        ],
        'startTrip' => [
            'label'        => 'Start',
            'heading'      => 'Start this trip?',
            'notification' => 'Trip started.',
        ],
        'completeTrip' => [
            'label'        => 'Complete',
            'heading'      => 'Complete this trip?',
            'notification' => 'Trip completed.',
        ],
    ],
];
