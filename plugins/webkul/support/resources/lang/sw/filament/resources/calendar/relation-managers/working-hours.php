<?php

return [
    'modal' => [
        'title' => 'Working Saa',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Jumla Taarifa',
                'fields' => [
                    'attendance-name' => 'Mahudhurio Jina',
                    'day-of-week'     => 'Siku ya Wiki',
                ],
            ],

            'timing-information' => [
                'title' => 'Timing Taarifa',

                'fields' => [
                    'day-period' => 'Siku Periods',
                    'week-type'  => 'Wiki Aina',
                    'work-from'  => 'Work Kutoka',
                    'work-to'    => 'Work Hadi',
                ],
            ],

            'date-information' => [
                'title' => 'Tarehe Taarifa',

                'fields' => [
                    'starting-date' => 'Starting Tarehe',
                    'ending-date'   => 'Ending Tarehe',
                ],
            ],

            'additional-information' => [
                'title' => 'Additional Taarifa',

                'fields' => [
                    'durations-days' => 'Duration (Siku)',
                    'display-type'   => 'Display Aina',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'          => 'Mahudhurio Jina',
            'day-of-week'   => 'Siku ya Wiki',
            'day-period'    => 'Siku Periods',
            'work-from'     => 'Work Kutoka',
            'work-to'       => 'Work Hadi',
            'starting-date' => 'Starting Tarehe',
            'ending-date'   => 'Ending Tarehe',
            'display-type'  => 'Display Aina',
            'created-by'    => 'Imeundwa Na',
            'created-at'    => 'Imeundwa',
            'updated-at'    => 'Imesasishwa',
        ],

        'groups' => [
            'activity-type' => 'Shughuli Aina',
            'assignment'    => 'Assignment',
            'assigned-to'   => 'Amekabidhiwa Hadi',
            'interval'      => 'Interval',
            'delay-unit'    => 'Delay Uniti',
            'delay-from'    => 'Delay Kutoka',
            'created-by'    => 'Imeundwa Na',
            'created-at'    => 'Imeundwa',
            'updated-at'    => 'Imesasishwa',
        ],

        'filters' => [
            'display-type' => 'Display Aina',
            'day-of-week'  => 'Siku ya Wiki',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Working Saa imesasishwa',
                    'body'  => 'working saa ina imesasishwa successfully.',
                ],
            ],

            'create' => [
                'notification' => [
                    'title' => 'Working Saa imeundwa',
                    'body'  => 'working saa ina imeundwa successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Working Saa imefutwa',
                    'body'  => 'working saa ina imefutwa successfully.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Working Saa restored',
                    'body'  => 'working saa ina restored successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Working Saa imefutwa',
                    'body'  => 'working saa ina imefutwa successfully.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Working Saa imefutwa',
                    'body'  => 'working saa ina imefutwa successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Working Saa imefutwa',
                    'body'  => 'working saa ina imefutwa successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Jumla Taarifa',

                'entries' => [
                    'name'        => 'Mahudhurio Jina',
                    'day-of-week' => 'Siku ya Wiki',
                ],
            ],

            'timing-information' => [
                'title' => 'Timing Taarifa',

                'entries' => [
                    'day-period' => 'Siku Periods',
                    'week-type'  => 'Wiki Aina',
                    'work-from'  => 'Work Kutoka',
                    'work-to'    => 'Work Hadi',
                ],
            ],

            'date-information' => [
                'title' => 'Tarehe Taarifa',

                'entries' => [
                    'starting-date' => 'Starting Tarehe',
                    'ending-date'   => 'Ending Tarehe',
                ],
            ],

            'additional-information' => [
                'title' => 'Additional Taarifa',

                'entries' => [
                    'durations-days' => 'Duration (Siku)',
                    'display-type'   => 'Display Aina',
                ],
            ],
        ],

        'note' => 'Maelezo',
    ],
];
