<?php

return [
    'banner' => [
        'message' => 'Ha iniciado sesión como :target (:company). Suplantación por :impersonator.',
        'ends-at' => 'Termina a las :time',
        'end'     => 'Terminar suplantación',
    ],

    'notifications' => [
        'started' => 'Ahora ha iniciado sesión como :name.',
        'refused' => 'La suplantación no se inició',

        'ended-automatically' => [
            'expired' => 'La suplantación alcanzó su límite de tiempo y ha terminado.',
            'invalid' => 'La suplantación ya no está permitida y ha terminado.',
        ],

        'granted' => 'Acceso a la suplantación concedido.',
        'revoked' => 'Acceso a la suplantación retirado.',
    ],

    'actions' => [
        'impersonate' => [
            'label'             => 'Suplantar',
            'modal-heading'     => 'Iniciar sesión como :name',
            'modal-description' => 'Actuará como este usuario, solo con sus permisos, durante un máximo de :minutes minutos. Todo lo que haga queda registrado con su nombre.',
            'submit'            => 'Iniciar suplantación',

            'form' => [
                'reason'   => 'Motivo',
                'password' => 'Su contraseña',
            ],
        ],

        'grant' => [
            'label'             => 'Permitir suplantación',
            'modal-description' => 'Este administrador multiempresa podrá suplantar a usuarios de las empresas que tiene asignadas.',
        ],

        'revoke' => [
            'label'             => 'Retirar acceso a la suplantación',
            'modal-description' => 'Una suplantación en curso termina en la siguiente carga de página.',
        ],
    ],
];
