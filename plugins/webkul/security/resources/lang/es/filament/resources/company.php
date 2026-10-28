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
    ],
];
