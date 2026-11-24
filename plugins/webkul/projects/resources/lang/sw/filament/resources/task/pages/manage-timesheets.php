<?php

return [
    'title' => 'Rejista za muda',

    'form' => [
        'date'                   => 'Tarehe',
        'employee'               => 'Mfanyakazi',
        'description'            => 'Maelezo',
        'time-spent'             => 'Muda uliotumika',
        'time-spent-helper-text' => 'Muda uliotumika kwa saa (mf. saa 1.5 ni saa 1 na dakika 30)',
    ],

    'table' => [
        'header-actions' => [
            'create' => [
                'label' => 'Ongeza rejista ya muda',

                'notification' => [
                    'title' => 'Rejista ya muda imeundwa',
                    'body'  => 'Rejista ya muda imeundwa kwa mafanikio.',
                ],
            ],
        ],

        'columns' => [
            'date'                   => 'Tarehe',
            'employee'               => 'Mfanyakazi',
            'description'            => 'Maelezo',
            'time-spent'             => 'Muda uliotumika',
            'time-spent-on-subtasks' => 'Muda uliotumika kwenye kazi ndogo',
            'total-time-spent'       => 'Jumla ya muda uliotumika',
            'remaining-time'         => 'Muda uliobaki',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Rejista ya muda imesasishwa',
                    'body'  => 'Rejista ya muda imesasishwa kwa mafanikio.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Rejista ya muda imefutwa',
                    'body'  => 'Rejista ya muda imefutwa kwa mafanikio.',
                ],
            ],
        ],
    ],
];
