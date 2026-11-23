<?php

return [
    'form' => [
        'date'                   => 'Tarehe',
        'employee'               => 'Mfanyakazi',
        'description'            => 'Maelezo',
        'time-spent'             => 'Time Spent',
        'time-spent-helper-text' => 'Time spent katika saa (Eg. 1.5 saa means 1 saa 30 dakika)',
    ],

    'table' => [
        'header-actions' => [
            'create' => [
                'label' => 'Ongeza Karatasi ya muda',

                'notification' => [
                    'title' => 'Karatasi ya muda imeundwa',
                    'body'  => 'karatasi ya muda ina imeundwa successfully.',
                ],
            ],
        ],

        'columns' => [
            'date'                   => 'Tarehe',
            'employee'               => 'Mfanyakazi',
            'description'            => 'Maelezo',
            'time-spent'             => 'Time Spent',
            'time-spent-on-subtasks' => 'Time Spent washa Subtasks',
            'total-time-spent'       => 'Jumla Time Spent',
            'remaining-time'         => 'Iliyobaki Time',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Karatasi ya muda imesasishwa',
                    'body'  => 'karatasi ya muda ina imesasishwa successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Karatasi ya muda imefutwa',
                    'body'  => 'karatasi ya muda ina imefutwa successfully.',
                ],
            ],
        ],
    ],
];
