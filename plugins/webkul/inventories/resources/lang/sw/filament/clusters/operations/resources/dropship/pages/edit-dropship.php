<?php

return [
    'notification' => [
        'title' => 'Usafirishaji wa moja kwa moja umesasishwa',
        'body'  => 'Usafirishaji wa moja kwa moja umesasishwa kwa mafanikio.',
    ],

    'header-actions' => [
        'print' => [
            'label' => 'Chapisha',
        ],

        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Usafirishaji wa moja kwa moja umefutwa',
                    'body'  => 'Usafirishaji wa moja kwa moja umefutwa kwa mafanikio.',
                ],

                'error' => [
                    'title' => 'Usafirishaji wa moja kwa moja haukuweza kufutwa',
                    'body'  => 'Usafirishaji wa moja kwa moja hauwezi kufutwa kwa sababu unatumika.',
                ],
            ],
        ],
    ],
];
