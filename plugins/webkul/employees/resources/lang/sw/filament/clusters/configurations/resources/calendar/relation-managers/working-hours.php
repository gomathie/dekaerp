<?php

return [
    'modal' => [
        'title' => 'Saa za kazi',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Taarifa za jumla',
                'fields' => [
                    'attendance-name' => 'Jina la kipindi',
                    'attendance-name' => 'Jina la kipindi',
                    'day-of-week'     => 'Siku ya wiki',
                ],
            ],

            'timing-information' => [
                'title' => 'Taarifa za muda',

                'fields' => [
                    'day-period' => 'Vipindi vya siku',
                    'week-type'  => 'Aina ya wiki',
                    'work-from'  => 'Kazi kuanzia',
                    'work-to'    => 'Kazi hadi',
                ],
            ],

            'date-information' => [
                'title' => 'Taarifa za tarehe',

                'fields' => [
                    'starting-date' => 'Tarehe ya kuanza',
                    'ending-date'   => 'Tarehe ya kumaliza',
                ],
            ],

            'additional-information' => [
                'title' => 'Taarifa za ziada',

                'fields' => [
                    'durations-days' => 'Muda (siku)',
                    'display-type'   => 'Aina ya maonyesho',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'          => 'Jina la kipindi',
            'day-of-week'   => 'Siku ya wiki',
            'day-period'    => 'Vipindi vya siku',
            'work-from'     => 'Kazi kuanzia',
            'work-to'       => 'Kazi hadi',
            'starting-date' => 'Tarehe ya kuanza',
            'ending-date'   => 'Tarehe ya kumaliza',
            'display-type'  => 'Aina ya maonyesho',
            'created-by'    => 'Imeundwa na',
            'created-at'    => 'Imeundwa',
            'updated-at'    => 'Imesasishwa',
        ],

        'groups' => [
            'activity-type' => 'Aina ya shughuli',
            'assignment'    => 'Ukabidhiwaji',
            'assigned-to'   => 'Amekabidhiwa',
            'interval'      => 'Muda kati',
            'delay-unit'    => 'Kipimo cha ucheleweshaji',
            'delay-from'    => 'Ucheleweshaji kuanzia',
            'created-by'    => 'Imeundwa na',
            'created-at'    => 'Imeundwa',
            'updated-at'    => 'Imesasishwa',
        ],

        'filters' => [
            'display-type' => 'Aina ya maonyesho',
            'day-of-week'  => 'Siku ya wiki',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Saa za kazi zimesasishwa',
                    'body'  => 'Saa za kazi zimesasishwa kikamilifu.',
                ],
            ],

            'create' => [
                'notification' => [
                    'title' => 'Saa za kazi zimeundwa',
                    'body'  => 'Saa za kazi zimeundwa kikamilifu.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Saa za kazi zimefutwa',
                    'body'  => 'Saa za kazi zimefutwa kikamilifu.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Saa za kazi zimerejeshwa',
                    'body'  => 'Saa za kazi zimerejeshwa kikamilifu.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Saa za kazi zimefutwa',
                    'body'  => 'Saa za kazi zimefutwa kikamilifu.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Saa za kazi zimefutwa',
                    'body'  => 'Saa za kazi zimefutwa kikamilifu.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Saa za kazi zimefutwa',
                    'body'  => 'Saa za kazi zimefutwa kikamilifu.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Taarifa za jumla',

                'entries' => [
                    'name'        => 'Jina la kipindi',
                    'day-of-week' => 'Siku ya wiki',
                ],
            ],

            'timing-information' => [
                'title' => 'Taarifa za muda',

                'entries' => [
                    'day-period' => 'Vipindi vya siku',
                    'week-type'  => 'Aina ya wiki',
                    'work-from'  => 'Kazi kuanzia',
                    'work-to'    => 'Kazi hadi',
                ],
            ],

            'date-information' => [
                'title' => 'Taarifa za tarehe',

                'entries' => [
                    'starting-date' => 'Tarehe ya kuanza',
                    'ending-date'   => 'Tarehe ya kumaliza',
                ],
            ],

            'additional-information' => [
                'title' => 'Taarifa za ziada',

                'entries' => [
                    'durations-days' => 'Muda (siku)',
                    'display-type'   => 'Aina ya maonyesho',
                ],
            ],
        ],

        'note' => 'Kidokezo',
    ],
];
