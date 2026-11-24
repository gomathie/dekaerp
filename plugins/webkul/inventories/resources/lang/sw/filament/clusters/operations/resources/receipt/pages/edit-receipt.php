<?php

return [
    'notification' => [
        'title' => 'Upokeaji umesasishwa',
        'body'  => 'Upokeaji umesasishwa kwa mafanikio.',
    ],

    'header-actions' => [
        'print' => [
            'label' => 'Chapisha',
        ],

        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Upokeaji umefutwa',
                    'body'  => 'Upokeaji umefutwa kwa mafanikio.',
                ],

                'error' => [
                    'title' => 'Upokeaji haukuweza kufutwa',
                    'body'  => 'Upokeaji hauwezi kufutwa kwa sababu unatumika.',
                ],
            ],
        ],
    ],
];
