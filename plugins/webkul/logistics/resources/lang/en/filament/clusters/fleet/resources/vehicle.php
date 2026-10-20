<?php

return [
    'navigation' => [
        'title' => 'Vehicles',
    ],

    'form' => [
        'fields' => [
            'registration-no'              => 'Registration number',
            'name'                         => 'Name',
            'company'                      => 'Company',
            'vehicle-type'                 => 'Vehicle type',
            'ownership'                    => 'Ownership',
            'carrier'                      => 'Carrier',
            'default-driver'               => 'Default driver',
            'equipment'                    => 'Maintenance equipment',
            'telematics-device-ref'        => 'Telematics device reference',
            'telematics-agent-id'          => 'Telematics agent ID',
            'telematics-agent-id-helper'   => 'The telematics platform’s own identifier for this vehicle, which is not the device IMEI. PILOT calls this the agent ID.',
            'capacity-kg'                  => 'Capacity weight',
            'capacity-m3'                  => 'Capacity volume',
            'is-active'                    => 'Active',
        ],
    ],

    'table' => [
        'columns' => [
            'registration-no'       => 'Registration',
            'name'                  => 'Name',
            'ownership'             => 'Ownership',
            'vehicle-type'          => 'Type',
            'carrier'               => 'Carrier',
            'capacity-kg'           => 'Capacity',
            'telematics-device-ref' => 'Telematics',
            'telematics-agent-id'   => 'Telematics agent',
            'company'               => 'Company',
            'is-active'             => 'Active',
        ],

        'filters' => [
            'ownership'    => 'Ownership',
            'vehicle-type' => 'Vehicle type',
        ],
    ],

    'infolist' => [
        'sections' => [
            'general'      => 'Vehicle',
            'capacity'     => 'Capacity',
            'integrations' => 'Integrations',
        ],
    ],

    'actions' => [
        'import-maintenance-equipment' => [
            'label' => 'Import from Maintenance equipment',

            'fields' => [
                'equipment' => 'Equipment',
            ],

            'notification' => [
                'title' => 'Maintenance equipment import finished',
                'body'  => ':created created, :skipped skipped.',
            ],
        ],
    ],
];
