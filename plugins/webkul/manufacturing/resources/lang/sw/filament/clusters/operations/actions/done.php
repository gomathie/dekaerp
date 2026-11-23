<?php

return [
    'label'         => 'Produce Zote',
    'partial-label' => 'Produce',

    'modal' => [
        'consumption-warning' => [
            'heading'     => 'Consumption Onyo',
            'description' => 'Baadhi bidhaa consumed different kiasi kuliko inatarajiwa. wewe want hadi validate uzalishaji agizo na sasa quantities?',

            'form' => [
                'product'    => 'Bidhaa',
                'to-consume' => 'Hadi Consume',
                'consumed'   => 'Consumed',
                'uom'        => 'Kipimo',
            ],

            'actions' => [
                'confirm' => [
                    'label' => 'Thibitisha',
                ],

                'set-quantities' => [
                    'label' => 'Weka Quantities na Thibitisha',
                ],
            ],
        ],

        'produced-warning' => [
            'heading'     => 'Produced ni different kuliko inatarajiwa',
            'description' => 'kiasi produced ni different kuliko inatarajiwa. wewe want hadi thibitisha uzalishaji agizo na sasa kiasi?',
        ],
    ],

    'notification' => [
        'success' => [
            'title' => 'Uzalishaji agizo imekamilika',
            'body'  => 'uzalishaji agizo ina imekamilika successfully.',
        ],
    ],
];
