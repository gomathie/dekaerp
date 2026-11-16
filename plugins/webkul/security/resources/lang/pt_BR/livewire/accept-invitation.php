<?php

return [
    'header' => [
        'sub-heading' => [
            'accept-invitation' => 'Aceitar convite',
        ],
    ],

    'title' => 'Cadastrar',

    'heading' => 'Cadastrar-se',

    'invitation-no-longer-authorized' => 'Este convite não está mais autorizado. Peça a um administrador que envie um novo convite.',

    'actions' => [

        'login' => [
            'before' => 'ou',
            'label'  => 'entrar na sua conta',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'Endereço de e-mail',
        ],

        'name' => [
            'label' => 'Nome',
        ],

        'password' => [
            'label'                => 'Senha',
            'validation_attribute' => 'senha',
        ],

        'password_confirmation' => [
            'label' => 'Confirmar senha',
        ],

        'actions' => [

            'register' => [
                'label' => 'Cadastrar-se',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Muitas tentativas de cadastro',
            'body'  => 'Tente novamente em :seconds segundos.',
        ],

    ],

];
