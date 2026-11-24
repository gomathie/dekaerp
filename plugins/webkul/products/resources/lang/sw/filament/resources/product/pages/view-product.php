<?php

return [
    'header-actions' => [
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

        'delete' => [
            'notification' => [
                'title' => 'Bidhaa imefutwa',
                'body'  => 'Bidhaa imefutwa kikamilifu.',
            ],
        ],
    ],
];
