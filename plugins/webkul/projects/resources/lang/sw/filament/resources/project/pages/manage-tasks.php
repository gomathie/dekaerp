<?php

return [
    'title' => 'Kazi',

    'header-actions' => [
        'create' => [
            'label' => 'Kazi mpya',
        ],
    ],

    'table' => [
        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Kazi imerejeshwa',
                    'body'  => 'Kazi imerejeshwa kwa mafanikio.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Kazi imefutwa',
                    'body'  => 'Kazi imefutwa kwa mafanikio.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Kazi imefutwa kabisa',
                    'body'  => 'Kazi imefutwa kabisa.',
                ],
            ],
        ],
    ],

    'tabs' => [
        'open-tasks'       => 'Kazi zilizo wazi',
        'my-tasks'         => 'Kazi zangu',
        'unassigned-tasks' => 'Kazi zisizokabidhiwa',
        'closed-tasks'     => 'Kazi zilizofungwa',
        'starred-tasks'    => 'Kazi zenye nyota',
        'archived-tasks'   => 'Kazi kwenye kumbukumbu',
    ],
];
