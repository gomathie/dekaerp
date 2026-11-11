<?php

return [
    'header' => [
        'sub-heading' => [
            'accept-invitation' => 'Aceptar invitación',
        ],
    ],

    'title' => 'Registrarse',

    'heading' => 'Crear cuenta',

    'invitation-no-longer-authorized' => 'Esta invitación ya no está autorizada. Pida a un administrador que envíe una nueva invitación.',

    'actions' => [

        'login' => [
            'before' => 'o',
            'label'  => 'inicie sesión en su cuenta',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'Correo electrónico',
        ],

        'name' => [
            'label' => 'Nombre',
        ],

        'password' => [
            'label'                => 'Contraseña',
            'validation_attribute' => 'contraseña',
        ],

        'password_confirmation' => [
            'label' => 'Confirmar contraseña',
        ],

        'actions' => [

            'register' => [
                'label' => 'Crear cuenta',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Demasiados intentos de registro',
            'body'  => 'Vuelva a intentarlo en :seconds segundos.',
        ],

    ],

];
