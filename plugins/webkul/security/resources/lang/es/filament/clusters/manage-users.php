<?php

return [
    'breadcrumb' => 'Gestionar usuarios',
    'title'      => 'Gestionar usuarios',
    'group'      => 'General',

    'navigation' => [
        'label' => 'Gestionar usuarios',
    ],

    'form' => [
        'enable-user-invitation' => [
            'label'       => 'Habilitar invitación de usuarios',
            'helper-text' => 'Permitir que los usuarios inviten a otros usuarios a la aplicación.',
        ],

        'enable-reset-password' => [
            'label'       => 'Habilitar restablecimiento de contraseña',
            'helper-text' => 'Permitir que los usuarios restablezcan su contraseña.',
        ],

        'default-role' => [
            'label'       => 'Rol predeterminado',
            'helper-text' => 'El rol predeterminado asignado a los nuevos usuarios.',
        ],
        'company-admin-business-role' => [
            'label'       => 'Rol de negocio del administrador de empresa',
            'helper-text' => 'Se asigna a cada nuevo administrador de empresa junto con Company Admin, para que pueda gestionar su empresa. No se ofrecen los roles de la plataforma.',
        ],

        'default-company' => [
            'label'       => 'Empresa predeterminada',
            'helper-text' => 'La empresa predeterminada asignada a los nuevos usuarios.',
        ],
    ],
];
