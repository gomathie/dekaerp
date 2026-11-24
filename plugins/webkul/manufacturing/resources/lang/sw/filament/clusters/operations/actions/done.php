<?php

return [
    'label'         => 'Zalisha zote',
    'partial-label' => 'Zalisha',

    'modal' => [
        'consumption-warning' => [
            'heading'     => 'Onyo la matumizi',
            'description' => 'Baadhi ya bidhaa zimetumika kwa idadi tofauti na ilivyotarajiwa. Unataka kuthibitisha oda ya uzalishaji kwa idadi za sasa?',

            'form' => [
                'product'    => 'Bidhaa',
                'to-consume' => 'Ya kutumia',
                'consumed'   => 'Imetumika',
                'uom'        => 'Kipimo',
            ],

            'actions' => [
                'confirm' => [
                    'label' => 'Thibitisha',
                ],

                'set-quantities' => [
                    'label' => 'Weka idadi na uthibitishe',
                ],
            ],
        ],

        'produced-warning' => [
            'heading'     => 'Kilichozalishwa ni tofauti na kilichotarajiwa',
            'description' => 'Idadi iliyozalishwa ni tofauti na ilivyotarajiwa. Unataka kuthibitisha oda ya uzalishaji kwa idadi ya sasa?',
        ],
    ],

    'notification' => [
        'success' => [
            'title' => 'Oda ya uzalishaji imekamilika',
            'body'  => 'Oda ya uzalishaji imekamilika kikamilifu.',
        ],
    ],
];
