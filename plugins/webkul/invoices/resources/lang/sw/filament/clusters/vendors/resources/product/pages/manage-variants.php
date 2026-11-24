<?php

return [
    'title' => 'Aina za bidhaa',

    'form' => [
        'date'                   => 'Tarehe',
        'employee'               => 'Mfanyakazi',
        'description'            => 'Maelezo',
        'time-spent'             => 'Muda uliotumika',
        'time-spent-helper-text' => 'Muda uliotumika kwa saa (mf. saa 1.5 ni saa 1 na dakika 30)',
    ],

    'table' => [
        'columns' => [
            'date'                   => 'Tarehe',
            'employee'               => 'Mfanyakazi',
            'description'            => 'Maelezo',
            'time-spent'             => 'Muda uliotumika',
            'time-spent-on-subtasks' => 'Muda kwenye kazi ndogo',
            'total-time-spent'       => 'Jumla ya muda uliotumika',
            'remaining-time'         => 'Muda uliobaki',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Aina ya bidhaa imefutwa',
                    'body'  => 'Aina ya bidhaa imefutwa kwa mafanikio.',
                ],
            ],
        ],
    ],
];
