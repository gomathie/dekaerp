<?php

return [
    'navigation' => [
        'title' => 'Conductores',
    ],

    'form' => [
        'fields' => [
            'company'            => 'Empresa',
            'employee'           => 'Empleado',
            'partner'            => 'Contacto del transportista',
            'name'               => 'Nombre',
            'phone'              => 'Teléfono',
            'license-number'     => 'Número de permiso',
            'license-class'      => 'Clase de permiso',
            'license-expires-at' => 'Vencimiento del permiso',
            'is-active'          => 'Activo',
        ],
    ],

    'table' => [
        'columns' => [
            'name'               => 'Nombre',
            'phone'              => 'Teléfono',
            'employee'           => 'Empleado',
            'partner'            => 'Contacto del transportista',
            'license-number'     => 'Número de permiso',
            'license-expires-at' => 'Vencimiento del permiso',
            'company'            => 'Empresa',
            'is-active'          => 'Activo',
        ],

        'filters' => [
            'license-attention' => 'Vencido o próximo a vencer',
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => 'Conductor',
            'license' => 'Permiso de conducir',
        ],
    ],

    'actions' => [
        'add-drivers-from-employees' => [
            'label' => 'Añadir conductores desde empleados',

            'fields' => [
                'employees' => 'Empleados',
            ],

            'notification' => [
                'title' => 'Importación de empleados finalizada',
                'body'  => ':created creados, :skipped omitidos.',
            ],
        ],
    ],
];
