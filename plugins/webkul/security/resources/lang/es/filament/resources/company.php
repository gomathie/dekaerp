<?php

return [
    'title' => 'Empresas',

    'navigation' => [
        'title' => 'Empresas',
    ],

    'global-search' => [
        'email' => 'Correo electrónico',
    ],

    'pages' => [
        'create' => [
            'administrator' => [
                'invited' => [
                    'title' => 'Administrador invitado',
                    'body'  => 'Se envió una invitación a :email. Esa persona elige su propia contraseña y después puede añadir los usuarios de su empresa.',
                ],

                'skipped' => [
                    'title'         => 'No se invitó a ningún administrador',
                    'missing-email' => 'La empresa se creó, pero no tiene dirección de correo, así que no se pudo invitar a nadie a administrarla. Añada una y envíe una invitación desde la página de Usuarios.',
                    'missing-role'  => 'La empresa se creó, pero el rol «Company Admin» no existe en esta instalación, así que no se pudo invitar a nadie.',

                    'seats-exhausted' => 'La empresa se ha creado, pero no le quedan plazas de usuario libres, así que nadie pudo ser invitado a administrarla. Aumente el límite de plazas y envíe una invitación desde la página de Usuarios.',
                    'invite-failed'   => 'La empresa se creó, pero no se pudo emitir la invitación. Puede que la dirección ya pertenezca a un usuario, o que la empresa aún no sea una que usted pueda asignar. Invite desde la página de Usuarios.',
                    'mail-failed'     => 'La empresa se creó y la invitación se emitió, pero no se pudo enviar el correo. Entregue este enlace de acceso a :email por un canal de confianza; caduca en 7 días y la persona elige su propia contraseña: :link',
                ],
            ],
        ],

        'edit' => [
            'administrator' => [
                'skipped' => [
                    'title'           => 'No se invitó a ningún administrador',
                    'disabled'        => 'Las invitaciones están desactivadas en los ajustes de usuarios, así que no se invitó a :email. Establezca una contraseña en su lugar o active las invitaciones.',
                    'missing-role'    => 'El rol «Company Admin» no existe en esta instalación, así que no se pudo invitar a nadie.',
                    'seats-exhausted' => 'A esta empresa no le quedan plazas de usuario libres, así que no se invitó a :email. Aumente primero el límite de plazas.',
                    'invite-failed'   => 'No se pudo emitir la invitación. Puede que la dirección ya pertenezca a un usuario, o que la empresa no sea una que usted pueda asignar. Para restablecer la contraseña de un administrador existente, introduzca también una contraseña.',
                    'mail-failed'     => 'La invitación se emitió, pero no se pudo enviar el correo. Entregue este enlace de acceso a :email por un canal de confianza; caduca en 7 días y la persona elige su propia contraseña: :link',
                ],
            ],
        ],

        'administrator-account' => [
            'created' => [
                'title' => 'Cuenta de administrador creada',
                'body'  => ':email ya puede iniciar sesión con la contraseña que usted estableció. No se envió por correo: entréguesela usted mismo.',
            ],

            'password-reset' => [
                'title' => 'Contraseña del administrador cambiada',
                'body'  => 'Se cambió la contraseña de :email. No se envió por correo: entréguesela usted mismo.',
            ],

            'refused' => [
                'title'             => 'No se cambió el administrador',
                'missing-email'     => 'Introduzca el correo del administrador junto con la contraseña.',
                'missing-role'      => 'El rol «Company Admin» no existe en esta instalación, así que no se creó ninguna cuenta.',
                'not-administrator' => ':email pertenece a un usuario que no es el administrador de esta empresa, así que su contraseña no se cambió aquí. Gestione ese usuario desde la página de Usuarios.',
                'not-allowed'       => 'No tiene permiso para gestionar el administrador de esta empresa.',
                'seats-exhausted'   => 'A esta empresa no le quedan plazas de usuario libres, así que no se creó ninguna cuenta para :email. Aumente primero el límite de plazas.',
            ],
        ],
    ],
];
