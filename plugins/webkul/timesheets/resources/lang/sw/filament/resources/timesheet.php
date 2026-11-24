<?php

return [
    'title' => 'Rejista za muda',

    'navigation' => [
        'title' => 'Rejista za muda',
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
        'time-spent'             => 'Muda uliotumika',
        'time-spent-helper-text' => 'Muda uliotumika kwa saa (mf. saa 1.5 ni saa 1 na dakika 30)',
    ],

    'table' => [
        'columns' => [
            'date'        => 'Tarehe',
            'employee'    => 'Mfanyakazi',
            'project'     => 'Mradi',
            'task'        => 'Kazi',
            'description' => 'Maelezo',
            'time-spent'  => 'Muda uliotumika',
            'created-at'  => 'Iliundwa',
            'updated-at'  => 'Ilisasishwa',
        ],

        'groups' => [
            'date'       => 'Tarehe',
            'employee'   => 'Mfanyakazi',
            'project'    => 'Mradi',
            'task'       => 'Kazi',
            'creator'    => 'Mwandishi',
        ],

        'filters' => [
            'date-from'  => 'Tarehe kuanzia',
            'date-until' => 'Tarehe hadi',
            'employee'   => 'Mfanyakazi',
            'project'    => 'Mradi',
            'task'       => 'Kazi',
            'creator'    => 'Mwandishi',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Rejista ya muda imesasishwa',
                    'body'  => 'Rejista ya muda imesasishwa kikamilifu.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Rejista ya muda imefutwa',
                    'body'  => 'Rejista ya muda imefutwa kikamilifu.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Rejista za muda zimefutwa',
                    'body'  => 'Rejista za muda zimefutwa kikamilifu.',
                ],
            ],
        ],
    ],
];
