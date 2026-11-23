<?php

return [
    'navigation' => [
        'title' => 'Magari',
    ],

    'form' => [
        'fields' => [
            'registration-no'              => 'Registration nambari',
            'name'                         => 'Jina',
            'company'                      => 'Kampuni',
            'vehicle-type'                 => 'Gari aina',
            'ownership'                    => 'Ownership',
            'carrier'                      => 'Carrier',
            'default-driver'               => 'Chaguo-msingi dereva',
            'equipment'                    => 'Matengenezo equipment',
            'telematics-device-ref'        => 'Telematics device kumbukumbu',
            'telematics-agent-id'          => 'Telematics agent Kitambulisho',
            'telematics-agent-id-helper'   => 'telematics platform’s yake identifier kwa hii gari, ambayo ni si device IMEI. PILOT calls hii agent Kitambulisho.',
            'capacity-kg'                  => 'Capacity uzito',
            'capacity-m3'                  => 'Capacity ujazo',
            'is-active'                    => 'Inatumika',
        ],
    ],

    'table' => [
        'columns' => [
            'registration-no'       => 'Registration',
            'name'                  => 'Jina',
            'ownership'             => 'Ownership',
            'vehicle-type'          => 'Aina',
            'carrier'               => 'Carrier',
            'capacity-kg'           => 'Capacity',
            'telematics-device-ref' => 'Telematics',
            'telematics-agent-id'   => 'Telematics agent',
            'company'               => 'Kampuni',
            'is-active'             => 'Inatumika',
        ],

        'filters' => [
            'ownership'    => 'Ownership',
            'vehicle-type' => 'Gari aina',
        ],
    ],

    'infolist' => [
        'sections' => [
            'general'      => 'Gari',
            'capacity'     => 'Capacity',
            'integrations' => 'Integrations',
        ],
    ],

    'actions' => [
        'import-maintenance-equipment' => [
            'label' => 'Ingiza kutoka Matengenezo equipment',

            'fields' => [
                'equipment' => 'Equipment',
            ],

            'notification' => [
                'title' => 'Matengenezo equipment ingiza finished',
                'body'  => ':created imeundwa, :skipped skipped.',
            ],
        ],
    ],
];
