<?php

return [
    'title' => 'Manage Maghala',

    'form' => [
        'enable-locations'                      => 'Maeneo',
        'enable-locations-helper-text'          => 'Track bidhaa mahali katika yako ghala',
        'configure-locations'                   => 'Configure Maeneo',
        'enable-multi-steps-routes'             => 'Multi Steps Njia',
        'enable-multi-steps-routes-helper-text' => 'Tumia yako yake njia hadi manage uhamisho ya bidhaa kati ya maghala',
        'configure-routes'                      => 'Configure Ghala Njia',
    ],

    'before-save' => [
        'notification' => [
            'warning' => [
                'title' => 'Ina multiple maghala',
                'body'  => 'Wewe can\'t deactivate multi-mahali ikiwa wewe ina zaidi kuliko one ghala.',
            ],
        ],
    ],
];
