<?php

return [
    'administrator' => [
        'title'       => 'Administrador',
        'description' => 'La persona que administra esta empresa y gestiona sus propios usuarios.',
        'none'        => 'Todavía no hay administrador para esta empresa.',
        'pending'     => 'invitación enviada, aún sin aceptar',
        'inactive'    => 'desactivado',
        'manage'      => 'Gestionar este usuario',

        'fields' => [
            'email'        => 'Correo del administrador',
            'email-helper' => 'Se envía una invitación a esta dirección, donde la persona establece su nombre y contraseña. Déjelo en blanco para usar el correo de la empresa.',
        ],
    ],
];
