<?php

return [
    'title'    => 'Ingia',
    'heading'  => 'Ingia',
    'messages' => [
        'failed' => 'Hizi credentials si kilinganacho yetu rekodi.',
    ],
    'notifications' => [
        'throttled' => [
            'title' => 'Mno nyingi attempts. Jaribu tena katika :seconds seconds.',
            'body'  => 'Tafadhali wait :seconds seconds (:minutes dakika) kabla ya trying tena.',
        ],
    ],
    'form' => [
        'email' => [
            'label' => 'Barua pepe anwani',
        ],
        'password' => [
            'label' => 'Nenosiri',
        ],
        'remember' => [
            'label' => 'Remember mimi',
        ],
        'actions' => [
            'authenticate' => [
                'label' => 'Ingia',
            ],
        ],
    ],
    'actions' => [
        'register' => [
            'before' => 'Don\'t ina akaunti?',
            'label'  => 'Unda akaunti',
        ],
        'request_password_reset' => [
            'label' => 'Umesahau nenosiri?',
        ],
    ],
];
