<?php

return [
    'title' => 'Usuarios y accesos',

    'table' => [
        'columns' => [
            'name'                 => 'Nombre',
            'login'                => 'Acceso (correo)',
            'company-admin'        => 'Administrador de la empresa',
            'active'               => 'Activo',
            'must-change-password' => 'Debe cambiar la contraseña',
        ],

        'empty-state' => [
            'heading'     => 'Aún no hay usuarios',
            'description' => 'Invite o cree al administrador en la sección Administrador de arriba.',
        ],

        'actions' => [
            'copy-invitation-link' => [
                'label'      => 'Copiar enlace de invitación',
                'invitation' => 'Invitación pendiente',
                'link'       => 'Enlace de invitación',
            ],
            'show-password' => [
                'label'             => 'Mostrar contraseña',
                'modal-heading'     => 'Mostrar la contraseña de :email',
                'modal-description' => 'Introduzca su propia contraseña para continuar. Esta consulta queda registrada en el registro de auditoría.',
                'submit'            => 'Mostrar contraseña',
                'shown'             => 'Contraseña de :email',

                'form' => [
                    'password' => 'Su contraseña',
                ],
            ],
            'set-password' => [
                'label'             => 'Establecer contraseña',
                'modal-heading'     => 'Establecer la contraseña de :email',
                'modal-description' => 'Las contraseñas actuales no se pueden mostrar porque se guardan cifradas en un solo sentido. Establezca una nueva y entréguesela usted mismo al usuario: no se envía por correo.',

                'form' => [
                    'password'             => 'Nueva contraseña',
                    'must-change-password' => 'Debe cambiar esta contraseña en el próximo inicio de sesión',
                ],
            ],
        ],
    ],
];
