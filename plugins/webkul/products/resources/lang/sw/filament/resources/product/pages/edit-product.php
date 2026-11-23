<?php

return [
    'notification' => [
        'title' => 'Bidhaa imesasishwa',
        'body'  => 'bidhaa ina imesasishwa successfully.',
    ],

    'header-actions' => [
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

        'delete' => [
            'notification' => [
                'title' => 'Bidhaa imefutwa',
                'body'  => 'bidhaa ina imefutwa successfully.',
            ],
        ],
    ],
];
