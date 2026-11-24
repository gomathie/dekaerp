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
            'time-spent-on-subtasks' => 'Muda uliotumika kwenye kazi ndogo',
            'total-time-spent'       => 'Jumla ya muda uliotumika',
            'remaining-time'         => 'Muda uliobaki',
            'variant-values'         => 'Thamani za aina',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Aina ya bidhaa imefutwa',
                    'body'  => 'Aina ya bidhaa imefutwa kikamilifu.',
                ],
            ],

            'view' => [
                'extra-footer-actions' => [
                    'print' => [
                        'label' => 'Chapisha lebo',

                        'form' => [
                            'fields' => [
                                'quantity' => 'Idadi ya lebo',
                                'format'   => 'Muundo',

                                'format-options' => [
                                    'dymo'       => 'Dymo',
                                    '2x7_price'  => '2x7 pamoja na bei',
                                    '4x7_price'  => '4x7 pamoja na bei',
                                    '4x12'       => '4x12',
                                    '4x12_price' => '4x12 pamoja na bei',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
];
