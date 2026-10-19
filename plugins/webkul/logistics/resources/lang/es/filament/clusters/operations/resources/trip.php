<?php

return [
    'navigation' => [
        'title' => 'Viajes',
    ],

    'form' => [
        'sections' => [
            'crew'     => 'Vehículo y equipo',
            'schedule' => 'Programación',
        ],

        'fields' => [
            'company'            => 'Empresa',
            'number'             => 'Número de viaje',
            'number-placeholder' => 'Se asigna automáticamente',
            'vehicle'            => 'Vehículo',
            'driver'             => 'Conductor',
            'dispatcher'         => 'Responsable de despacho',
            'planned-start-at'   => 'Inicio planificado',
            'planned-end-at'     => 'Fin planificado',
            'odometer-start'     => 'Odómetro inicial',
            'odometer-end'       => 'Odómetro final',
            'notes'              => 'Notas',
        ],
    ],

    'table' => [
        'columns' => [
            'number'           => 'Viaje',
            'state'            => 'Estado',
            'vehicle'          => 'Vehículo',
            'driver'           => 'Conductor',
            'shipments'        => 'Envíos',
            'planned-start-at' => 'Inicio planificado',
            'company'          => 'Empresa',
        ],

        'filters' => [
            'state'   => 'Estado',
            'vehicle' => 'Vehículo',
            'driver'  => 'Conductor',
        ],
    ],

    'infolist' => [
        'summary'         => 'Viaje',
        'schedule'        => 'Programación',
        'load'            => 'Carga',
        'actual-start-at' => 'Inicio real',
        'actual-end-at'   => 'Fin real',
        'capacity'        => 'Capacidad',
        'capacity-ok'     => 'Dentro de la capacidad del vehículo.',
    ],

    'relations' => [
        'shipments' => [
            'title'   => 'Envíos',
            'columns' => [
                'number'   => 'Envío',
                'state'    => 'Estado',
                'customer' => 'Cliente',
                'weight'   => 'Peso (kg)',
                'volume'   => 'Volumen (m³)',
            ],
            'actions' => [
                'attach' => [
                    'label'        => 'Añadir envío',
                    'field'        => 'Envío confirmado',
                    'notification' => 'Envío añadido a este viaje.',
                ],
            ],
        ],
    ],

    'actions' => [
        'dispatchTrip' => [
            'label'        => 'Despachar',
            'heading'      => '¿Despachar este viaje?',
            'notification' => 'Viaje despachado.',
        ],
        'startTrip' => [
            'label'        => 'Iniciar',
            'heading'      => '¿Iniciar este viaje?',
            'notification' => 'Viaje iniciado.',
        ],
        'completeTrip' => [
            'label'        => 'Completar',
            'heading'      => '¿Completar este viaje?',
            'notification' => 'Viaje completado.',
        ],
    ],
];
