<?php

return [
    'header' => [
        'sub-heading' => [
            'accept-invitation' => 'Accept Invitation',
        ],
    ],

    'title' => 'Register',

    'heading' => 'Sign up',

    'invitation-no-longer-authorized' => 'This invitation is no longer authorized. Ask an administrator to send a new invitation.',

    'actions' => [

        'login' => [
            'before' => 'or',
            'label'  => 'sign in to your account',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'Email address',
        ],

        'name' => [
            'label' => 'Name',
        ],

        'password' => [
            'label'                => 'Password',
            'validation_attribute' => 'password',
        ],

        'password_confirmation' => [
            'label' => 'Confirm password',
        ],

        'actions' => [

            'register' => [
                'label' => 'Sign up',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Too many registration attempts',
            'body'  => 'Please try again in :seconds seconds.',
        ],

    ],

];
