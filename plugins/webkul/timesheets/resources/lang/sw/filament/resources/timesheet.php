<?php

return [
    'title' => 'Karatasi za muda',

    'navigation' => [
        'title' => 'Karatasi za muda',
    ],

    'global-search' => [
        'project' => 'Mradi',
        'task'    => 'Kazi',
        'date'    => 'Tarehe',
    ],

    'form' => [
        'date'                   => 'Tarehe',
        'employee'               => 'Mfanyakazi',
        'project'                => 'Mradi',
        'task'                   => 'Kazi',
        'description'            => 'Maelezo',
        'time-spent'             => 'Time Spent',
        'time-spent-helper-text' => 'Time spent katika saa (Eg. 1.5 saa means 1 saa 30 dakika)',
    ],

    'table' => [
        'columns' => [
            'date'        => 'Tarehe',
            'employee'    => 'Mfanyakazi',
            'project'     => 'Mradi',
            'task'        => 'Kazi',
            'description' => 'Maelezo',
            'time-spent'  => 'Time Spent',
            'created-at'  => 'Imeundwa',
            'updated-at'  => 'Imesasishwa',
        ],

        'groups' => [
            'date'       => 'Tarehe',
            'employee'   => 'Mfanyakazi',
            'project'    => 'Mradi',
            'task'       => 'Kazi',
            'creator'    => 'Creator',
        ],

        'filters' => [
            'date-from'  => 'Tarehe Kutoka',
            'date-until' => 'Tarehe Mpaka',
            'employee'   => 'Mfanyakazi',
            'project'    => 'Mradi',
            'task'       => 'Kazi',
            'creator'    => 'Creator',
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

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Karatasi za muda imefutwa',
                    'body'  => 'karatasi za muda ina imefutwa successfully.',
                ],
            ],
        ],
    ],
];
