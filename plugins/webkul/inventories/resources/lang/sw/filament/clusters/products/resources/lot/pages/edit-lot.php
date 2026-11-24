<?php

return [
    'notification' => [
        'title' => 'Kundi limesasishwa',
        'body'  => 'Kundi limesasishwa kwa mafanikio.',
    ],

    'header-actions' => [
        'print' => [
            'label' => 'Chapisha',
        ],

        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Kundi limefutwa',
                    'body'  => 'Kundi limefutwa kwa mafanikio.',
                ],

                'error' => [
                    'title' => 'Kundi halikuweza kufutwa',
                    'body'  => 'Kundi haliwezi kufutwa kwa sababu linatumika.',
                ],
            ],
        ],
    ],
];
