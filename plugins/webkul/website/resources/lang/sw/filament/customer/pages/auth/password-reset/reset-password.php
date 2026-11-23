<?php

return [
    'title'         => 'Weka upya nenosiri',
    'heading'       => 'Weka upya nenosiri',
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
            'label'                => 'Mpya nenosiri',
            'validation_attribute' => 'nenosiri',
        ],
        'password_confirmation' => [
            'label' => 'Thibitisha mpya nenosiri',
        ],
        'actions' => [
            'reset' => [
                'label' => 'Weka upya nenosiri',
            ],
        ],
    ],
];
