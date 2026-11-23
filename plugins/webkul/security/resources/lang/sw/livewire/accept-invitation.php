<?php

return [
    'header' => [
        'sub-heading' => [
            'accept-invitation' => 'Accept Mwaliko',
        ],
    ],

    'title' => 'Jisajili',

    'heading' => 'Sign juu',

    'invitation-no-longer-authorized' => 'Hii mwaliko ni hakuna longer authorized. Ask msimamizi hadi send mpya mwaliko.',

    'actions' => [

        'login' => [
            'before' => 'au',
            'label'  => 'ingia hadi yako akaunti',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'Barua pepe anwani',
        ],

        'name' => [
            'label' => 'Jina',
        ],

        'password' => [
            'label'                => 'Nenosiri',
            'validation_attribute' => 'nenosiri',
        ],

        'password_confirmation' => [
            'label' => 'Thibitisha nenosiri',
        ],

        'actions' => [

            'register' => [
                'label' => 'Sign juu',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Mno nyingi registration attempts',
            'body'  => 'Tafadhali jaribu tena katika :seconds seconds.',
        ],

    ],

];
