<?php

return [
    'header' => [
        'sub-heading' => [
            'accept-invitation' => 'Accepter l’invitation',
        ],
    ],

    'title' => 'S’inscrire',

    'heading' => 'Créer un compte',

    'invitation-no-longer-authorized' => 'Cette invitation n’est plus autorisée. Demandez à un administrateur d’envoyer une nouvelle invitation.',

    'actions' => [

        'login' => [
            'before' => 'ou',
            'label'  => 'connectez-vous à votre compte',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'Adresse e-mail',
        ],

        'name' => [
            'label' => 'Nom',
        ],

        'password' => [
            'label'                => 'Mot de passe',
            'validation_attribute' => 'mot de passe',
        ],

        'password_confirmation' => [
            'label' => 'Confirmer le mot de passe',
        ],

        'actions' => [

            'register' => [
                'label' => 'S’inscrire',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Trop de tentatives d’inscription',
            'body'  => 'Veuillez réessayer dans :seconds secondes.',
        ],

    ],

];
