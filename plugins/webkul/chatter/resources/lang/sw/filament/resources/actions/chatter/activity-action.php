<?php

return [
    'setup' => [
        'title'               => 'Ratiba Shughuli',
        'submit-action-title' => 'Ratiba',

        'form' => [
            'fields' => [
                'activity-plan' => 'Shughuli Plan',
                'plan-date'     => 'Plan Tarehe',
                'plan-summary'  => 'Plan Muhtasari',
                'activity-type' => 'Shughuli Aina',
                'due-date'      => 'Tarehe ya malipo',
                'summary'       => 'Muhtasari',
                'assigned-to'   => 'Amekabidhiwa Hadi',
                'log-note'      => 'Log maelezo',
                'body'          => 'Aina yako ujumbe hapa',
            ],
        ],

        'actions' => [
            'notification' => [
                'success' => [
                    'title' => 'Shughuli Imeundwa',
                    'body'  => 'shughuli ina imeundwa.',
                ],

                'warning'  => [
                    'title' => 'Hakuna mpya faili',
                    'body'  => 'Zote faili ina tayari uploaded.',
                ],

                'error' => [
                    'title' => 'Shughuli creation imeshindwa',
                    'body'  => 'Imeshindwa hadi unda shughuli ',
                ],
            ],
        ],
    ],
];
