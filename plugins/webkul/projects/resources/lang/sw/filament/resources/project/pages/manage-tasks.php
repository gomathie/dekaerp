<?php

return [
    'title' => 'Kazi',

    'header-actions' => [
        'create' => [
            'label' => 'Mpya Kazi',
        ],
    ],

    'table' => [
        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Kazi restored',
                    'body'  => 'kazi ina restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Kazi imefutwa',
                    'body'  => 'kazi ina imefutwa successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Kazi force imefutwa',
                    'body'  => 'kazi ina force imefutwa successfully.',
                ],
            ],
        ],
    ],

    'tabs' => [
        'open-tasks'       => 'Wazi Kazi',
        'my-tasks'         => 'Yangu Kazi',
        'unassigned-tasks' => 'Hajakabidhiwa Kazi',
        'closed-tasks'     => 'Imefungwa Kazi',
        'starred-tasks'    => 'Starred Kazi',
        'archived-tasks'   => 'Imehifadhiwa Kazi',
    ],
];
