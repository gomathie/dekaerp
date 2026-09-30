<?php

return [
    'navigation' => [
        'title' => 'Vehículos',
    ],

    'form' => [
        'fields' => [
            'registration-no'       => 'Número de matrícula',
            'name'                  => 'Nombre',
            'company'               => 'Empresa',
            'vehicle-type'          => 'Tipo de vehículo',
            'ownership'             => 'Propiedad',
            'carrier'               => 'Transportista',
            'default-driver'        => 'Conductor predeterminado',
            'equipment'             => 'Equipo de mantenimiento',
            'telematics-device-ref' => 'Referencia del dispositivo telemático',
            'capacity-kg'           => 'Capacidad de peso',
            'capacity-m3'           => 'Capacidad de volumen',
            'is-active'             => 'Activo',
        ],
    ],

    'table' => [
        'columns' => [
            'registration-no'       => 'Matrícula',
            'name'                  => 'Nombre',
            'ownership'             => 'Propiedad',
            'vehicle-type'          => 'Tipo',
            'carrier'               => 'Transportista',
            'capacity-kg'           => 'Capacidad',
            'telematics-device-ref' => 'Telemática',
            'company'               => 'Empresa',
            'is-active'             => 'Activo',
        ],

        'filters' => [
            'ownership'    => 'Propiedad',
            'vehicle-type' => 'Tipo de vehículo',
        ],
    ],

    'infolist' => [
        'sections' => [
            'general'      => 'Vehículo',
            'capacity'     => 'Capacidad',
            'integrations' => 'Integraciones',
        ],
    ],

    'actions' => [
        'import-maintenance-equipment' => [
            'label' => 'Importar desde equipos de Mantenimiento',

            'fields' => [
                'equipment' => 'Equipo',
            ],

            'notification' => [
                'title' => 'Importación de equipos de mantenimiento finalizada',
                'body'  => ':created creados, :skipped omitidos.',
            ],
        ],
    ],
];
