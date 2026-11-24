<?php

return [
    'setup' => [
        'title'               => 'Panga shughuli',
        'submit-action-title' => 'Panga',

        'form' => [
            'fields' => [
                'activity-plan' => 'Mpango wa shughuli',
                'plan-date'     => 'Tarehe ya mpango',
                'plan-summary'  => 'Muhtasari wa mpango',
                'activity-type' => 'Aina ya shughuli',
                'due-date'      => 'Tarehe ya mwisho',
                'summary'       => 'Muhtasari',
                'assigned-to'   => 'Amekabidhiwa',
                'log-note'      => 'Andika kidokezo',
                'body'          => 'Andika ujumbe wako hapa',
            ],
        ],

        'actions' => [
            'notification' => [
                'success' => [
                    'title' => 'Shughuli imeundwa',
                    'body'  => 'Shughuli imeundwa.',
                ],

                'warning'  => [
                    'title' => 'Hakuna faili mpya',
                    'body'  => 'Faili zote zimeshapakiwa.',
                ],

                'error' => [
                    'title' => 'Imeshindwa kuunda shughuli',
                    'body'  => 'Imeshindwa kuunda shughuli ',
                ],
            ],
        ],
    ],
];
