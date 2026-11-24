<?php

return [
    'title'         => 'Weka upya nenosiri',
    'heading'       => 'Weka upya nenosiri',
    'notifications' => [
        'throttled' => [
            'title' => 'Majaribio mengi mno. Jaribu tena baada ya sekunde :seconds.',
            'body'  => 'Tafadhali subiri sekunde :seconds (dakika :minutes) kabla ya kujaribu tena.',
        ],
    ],
    'form' => [
        'email' => [
            'label' => 'Anwani ya barua pepe',
        ],
        'password' => [
            'label'                => 'Nenosiri jipya',
            'validation_attribute' => 'nenosiri',
        ],
        'password_confirmation' => [
            'label' => 'Thibitisha nenosiri jipya',
        ],
        'actions' => [
            'reset' => [
                'label' => 'Weka upya nenosiri',
            ],
        ],
    ],
];
