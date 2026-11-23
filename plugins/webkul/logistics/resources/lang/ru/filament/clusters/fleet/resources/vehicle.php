<?php

return [
    'navigation' => [
        'title' => 'Транспорт',
    ],

    'form' => [
        'fields' => [
            'registration-no'              => 'Registration номер',
            'name'                         => 'Название',
            'company'                      => 'Компания',
            'vehicle-type'                 => 'Транспорт тип',
            'ownership'                    => 'Ownership',
            'carrier'                      => 'Carrier',
            'default-driver'               => 'По умолчанию водитель',
            'equipment'                    => 'Обслуживание equipment',
            'telematics-device-ref'        => 'Telematics device ссылка',
            'telematics-agent-id'          => 'Telematics agent Ид.',
            'telematics-agent-id-helper'   => 'telematics platform’s собственную identifier для этот транспорт, который не device IMEI. PILOT calls этот agent Ид..',
            'capacity-kg'                  => 'Capacity вес',
            'capacity-m3'                  => 'Capacity объём',
            'is-active'                    => 'Активен',
        ],
    ],

    'table' => [
        'columns' => [
            'registration-no'       => 'Registration',
            'name'                  => 'Название',
            'ownership'             => 'Ownership',
            'vehicle-type'          => 'Тип',
            'carrier'               => 'Carrier',
            'capacity-kg'           => 'Capacity',
            'telematics-device-ref' => 'Telematics',
            'telematics-agent-id'   => 'Telematics agent',
            'company'               => 'Компания',
            'is-active'             => 'Активен',
        ],

        'filters' => [
            'ownership'    => 'Ownership',
            'vehicle-type' => 'Транспорт тип',
        ],
    ],

    'infolist' => [
        'sections' => [
            'general'      => 'Транспорт',
            'capacity'     => 'Capacity',
            'integrations' => 'Integrations',
        ],
    ],

    'actions' => [
        'import-maintenance-equipment' => [
            'label' => 'Импорт от Обслуживание equipment',

            'fields' => [
                'equipment' => 'Equipment',
            ],

            'notification' => [
                'title' => 'Обслуживание equipment импорт finished',
                'body'  => ':created создано, :skipped skipped.',
            ],
        ],
    ],
];
