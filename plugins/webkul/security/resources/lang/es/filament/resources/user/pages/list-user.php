<?php

return [
    'tabs' => [
        'all'      => 'Todos los usuarios',
        'archived' => 'Usuarios archivados',
    ],

    'header-actions' => [
        'invitation' => 'Invitación pendiente',
        'resend'     => [
            'title'         => 'Reenviar invitación',
            'invited'       => 'Invitación reenviada',
            'invite-failed' => 'No se pudo reenviar la invitación',
            'mail-failed'   => 'No se pudo enviar el correo',
            'recovery'      => 'La invitación sigue activa. Comparta este enlace por un canal seguro: :link',
        ],
        'revoke' => [
            'title' => 'Revocar invitación',
            'done'  => 'Invitación revocada',
        ],
        'invite' => [
            'title' => 'Invitar usuario',
            'modal' => [
                'submit-action-label' => 'Invitar usuario',
            ],
            'form' => [
                'email' => 'Correo electrónico',
            ],
            'notification' => [
                'success' => [
                    'title' => 'Usuario invitado',
                    'body'  => 'El usuario se ha invitado correctamente',
                ],
                'error' => [
                    'title' => 'Error al invitar al usuario',
                    'body'  => 'El sistema encontró un error inesperado al intentar enviar la invitación del usuario.',
                ],

                'default-company-error' => [
                    'title' => 'Empresa predeterminada no establecida',
                    'body'  => 'Establezca la empresa predeterminada en la configuración antes de invitar a un usuario.',
                ],
            ],
        ],

        'create' => [
            'label' => 'Nuevo usuario',
        ],
    ],
];
