<?php

return [
    'notification' => [
        'title' => 'Uwasilishaji umesasishwa',
        'body'  => 'Uwasilishaji umesasishwa kwa mafanikio.',
    ],

    'header-actions' => [
        'print' => [
            'label' => 'Chapisha',
        ],

        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Uwasilishaji umefutwa',
                    'body'  => 'Uwasilishaji umefutwa kwa mafanikio.',
                ],

                'error' => [
                    'title' => 'Uwasilishaji haukuweza kufutwa',
                    'body'  => 'Uwasilishaji hauwezi kufutwa kwa sababu unatumika.',
                ],
            ],
        ],
    ],
];
