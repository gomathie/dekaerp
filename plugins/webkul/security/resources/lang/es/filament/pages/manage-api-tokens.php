<?php

return [
    'navigation' => [
        'label' => 'Tokens de API',
    ],

    'group'      => 'General',
    'breadcrumb' => 'Tokens de API',
    'title'      => 'Tokens de API',
    'subheading' => 'Tokens que las integraciones de clientes usan para autenticarse en la API. Un token tiene los roles y las empresas permitidas del usuario al que pertenece.',

    'abilities' => [
        'read'  => 'Solo lectura',
        'write' => 'Escritura',
        'all'   => 'Acceso completo',
    ],

    'table' => [
        'columns' => [
            'name'      => 'Etiqueta',
            'user'      => 'Actúa como',
            'abilities' => 'Alcances',
            'last-used' => 'Último uso',
            'expires'   => 'Caduca',
            'created'   => 'Emitido',
        ],

        'never-used' => 'Nunca usado',
    ],

    'actions' => [
        'issue' => [
            'label'  => 'Emitir token',
            'submit' => 'Emitir token',

            'fields' => [
                'user'             => 'Actúa como usuario',
                'user-helper'      => 'El token puede hacer exactamente lo que puede hacer este usuario. Use un usuario dedicado por cliente, nunca un administrador.',
                'name'             => 'Etiqueta',
                'name-helper'      => 'Cómo reconocerá este token más adelante, p. ej. acme-warehouse-sync.',
                'abilities'        => 'Alcances',
                'abilities-helper' => 'Solo lectura no permite POST, PATCH ni DELETE. Acceso completo omite las comprobaciones de alcance.',
            ],
        ],

        'revoke' => [
            'label'       => 'Revocar',
            'heading'     => '¿Revocar este token?',
            'description' => 'La integración que lo usa dejará de funcionar de inmediato. Esta acción no se puede deshacer.',
            'bulk'        => 'Revocar seleccionados',
        ],
    ],

    'notifications' => [
        'issued' => [
            'title' => 'Token emitido',
            'body'  => 'Cópielo ahora: no se podrá volver a mostrar.',
        ],

        'revoked'      => 'Token revocado',
        'missing-user' => 'Ese usuario ya no existe.',
    ],

    'plaintext' => [
        'title'       => 'Copie este token ahora',
        'description' => 'Se almacena solo como hash, por lo que esta es la única vez que puede mostrarse. Envíelo al cliente por un canal de confianza.',
    ],

    'empty' => [
        'heading'     => 'Aún no hay tokens de API',
        'description' => 'Emita uno para que una integración de cliente pueda autenticarse en la API.',
    ],
];
