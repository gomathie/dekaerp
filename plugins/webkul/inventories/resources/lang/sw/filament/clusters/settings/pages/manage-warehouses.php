<?php

return [
    'title' => 'Simamia maghala',

    'form' => [
        'enable-locations'                      => 'Maeneo',
        'enable-locations-helper-text'          => 'Fuatilia eneo la bidhaa ghalani mwako',
        'configure-locations'                   => 'Sanidi maeneo',
        'enable-multi-steps-routes'             => 'Njia za hatua nyingi',
        'enable-multi-steps-routes-helper-text' => 'Tumia njia zako mwenyewe kusimamia uhamisho wa bidhaa kati ya maghala',
        'configure-routes'                      => 'Sanidi njia za ghala',
    ],

    'before-save' => [
        'notification' => [
            'warning' => [
                'title' => 'Kuna maghala mengi',
                'body'  => 'Huwezi kuzima maeneo mengi ikiwa una zaidi ya ghala moja.',
            ],
        ],
    ],
];
