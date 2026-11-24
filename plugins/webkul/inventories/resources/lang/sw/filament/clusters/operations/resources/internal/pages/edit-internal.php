<?php

return [
    'notification' => [
        'title' => 'Uhamisho wa ndani umesasishwa',
        'body'  => 'Uhamisho wa ndani umesasishwa kwa mafanikio.',
    ],

    'header-actions' => [
        'print' => [
            'label' => 'Chapisha',
        ],

        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Uhamisho wa ndani umefutwa',
                    'body'  => 'Uhamisho wa ndani umefutwa kwa mafanikio.',
                ],

                'error' => [
                    'title' => 'Uhamisho wa ndani haukuweza kufutwa',
                    'body'  => 'Uhamisho wa ndani hauwezi kufutwa kwa sababu unatumika.',
                ],
            ],
        ],
    ],
];
