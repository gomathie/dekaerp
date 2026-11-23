<?php

return [
    'title' => 'Variants',

    'form' => [
        'date'                   => 'Tarehe',
        'employee'               => 'Mfanyakazi',
        'description'            => 'Maelezo',
        'time-spent'             => 'Time Spent',
        'time-spent-helper-text' => 'Time spent katika saa (Eg. 1.5 saa means 1 saa 30 dakika)',
    ],

    'table' => [
        'columns' => [
            'date'                   => 'Tarehe',
            'employee'               => 'Mfanyakazi',
            'description'            => 'Maelezo',
            'time-spent'             => 'Time Spent',
            'time-spent-on-subtasks' => 'Time Spent washa Subtasks',
            'total-time-spent'       => 'Jumla Time Spent',
            'remaining-time'         => 'Iliyobaki Time',
            'variant-values'         => 'Variant Values',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Variant imefutwa',
                    'body'  => 'variant ina imefutwa successfully.',
                ],
            ],

            'view' => [
                'extra-footer-actions' => [
                    'print' => [
                        'label' => 'Chapisha Labels',

                        'form' => [
                            'fields' => [
                                'quantity' => 'Nambari ya Labels',
                                'format'   => 'Umbizo',

                                'format-options' => [
                                    'dymo'       => 'Dymo',
                                    '2x7_price'  => '2x7 na bei',
                                    '4x7_price'  => '4x7 na bei',
                                    '4x12'       => '4x12',
                                    '4x12_price' => '4x12 na bei',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
];
