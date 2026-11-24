<?php

return [
    'notification' => [
        'title' => 'Makubaliano ya ununuzi yamesasishwa',
        'body'  => 'Makubaliano ya ununuzi yamesasishwa.',
    ],

    'header-actions' => [
        'confirm' => [
            'label' => 'Thibitisha',

            'notification' => [
                'unable' => [
                    'title' => 'Imeshindikana kuthibitisha makubaliano ya ununuzi',
                    'body'  => 'Ongeza angalau mstari mmoja wa bidhaa kabla ya kuthibitisha makubaliano haya.',
                ],
            ],
        ],

        'close' => [
            'label'        => 'Funga',
            'notification' => [
                'warning' => [
                    'title' => 'Imeshindikana kufunga makubaliano ya ununuzi',
                    'body'  => 'Huwezi kufunga makubaliano haya kwa sababu baadhi ya maombi ya nukuu yanayohusiana hayako katika hali ya Imekamilika au Imeghairiwa.',
                ],
            ],
        ],

        'cancel' => [
            'label' => 'Ghairi',
        ],

        'print' => [
            'label' => 'Chapisha',
        ],

        'delete' => [
            'notification' => [
                'title' => 'Makubaliano ya ununuzi yamefutwa',
                'body'  => 'Makubaliano ya ununuzi yamefutwa.',
            ],
        ],
    ],
];
