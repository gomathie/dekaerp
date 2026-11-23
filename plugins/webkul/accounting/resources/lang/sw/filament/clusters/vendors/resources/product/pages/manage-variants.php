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
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Variant imefutwa',
                    'body'  => 'variant ina imefutwa successfully.',
                ],
            ],
        ],
    ],
];
