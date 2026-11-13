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
            'email'                => 'Correo del administrador',
            'email-helper'         => 'Sin contraseña, se envía una invitación a esta dirección y la persona elige su propia contraseña. Déjelo en blanco para invitar al correo de la empresa.',
            'email-helper-edit'    => 'Introduzca una dirección para invitar a un nuevo administrador. Añada una contraseña para crear su cuenta al momento, o para restablecer la contraseña del administrador de esta empresa. Déjelo en blanco para no cambiar nada.',
            'password'             => 'Contraseña del administrador',
            'password-helper'      => 'Opcional. Establece la contraseña ahora en lugar de enviar una invitación, o la restablece para el administrador de esta empresa. Nunca se envía por correo: entréguesela usted mismo.',
            'must-change-password' => 'Debe cambiar esta contraseña en el próximo inicio de sesión',
            'name'                 => 'Nombre del administrador',
        ],
    ],

    'account' => [
        'title'       => 'Tipo de cuenta',
        'description' => 'Si esta empresa está en prueba o es comercial. Solo el personal de instalación puede elegirlo, y solo al crear la empresa.',

        'fields' => [
            'type'         => 'Tipo de cuenta',
            'commercial'   => 'Comercial',
            'test'         => 'Prueba',
            'days'         => 'Días de prueba',
            'days-helper'  => 'Cuántos días dura la prueba. Puede añadir más después. Una prueba no se factura.',
        ],
    ],
];
