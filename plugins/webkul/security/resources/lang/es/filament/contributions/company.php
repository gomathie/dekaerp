<?php

return [
    'administrator' => [
        'title'       => 'Administrador',
        'description' => 'La persona que administra esta empresa y gestiona sus propios usuarios.',
        'none'        => 'Todavía no hay administrador para esta empresa.',
        'pending'     => 'invitación enviada, aún sin aceptar',
        'inactive'    => 'desactivado',
        'manage'      => 'Gestionar este usuario',

        'status' => [
            'active'   => 'Activo',
            'inactive' => 'Desactivado',
        ],

        'set-password' => [
            'label'       => 'Establecer contraseña',
            'heading'     => 'Nueva contraseña para :email',
            'description' => 'No se envía por correo: entréguesela usted mismo.',
        ],

        'add' => [
            'label'                => 'Añadir administrador',
            'heading'              => 'Añadir un administrador',
            'submit'               => 'Añadir',
            'method'               => '¿Cómo debe obtener acceso?',
            'invite'               => 'Enviar una invitación',
            'invite-description'   => 'Recibe un correo y elige su propia contraseña.',
            'password'             => 'Establecer una contraseña ahora',
            'password-description' => 'Para cuando el correo no llega o el cliente llama. También restablece la contraseña de alguien que ya está en esta empresa.',
        ],

        'invitation' => [
            'waiting'          => 'Invitación enviada, aún sin aceptar',
            'pending'          => 'Pendiente',
            'expires'          => 'Pendiente hasta el :date',
            'resend'           => 'Reenviar',
            'copy-link'        => 'Copiar enlace',
            'copy-heading'     => 'Enlace de acceso para :email',
            'copy-description' => 'Cualquiera con este enlace puede configurar esta cuenta de administrador. Envíelo solo a esa persona, por un canal de confianza.',
            'link'             => 'Enlace',
        ],

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

    'currencies' => [
        'title'       => 'Monedas',
        'description' => 'Monedas que esta empresa puede usar en facturas, facturas de proveedor, presupuestos y pedidos de compra, además de su moneda predeterminada. Déjelo vacío para permitir todas las monedas activas.',
        'fields'      => [
            'permitted' => 'Monedas adicionales',
            'available' => 'Disponibles ahora en los documentos: :list',
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
