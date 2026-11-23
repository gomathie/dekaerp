<?php

return [
    'title' => 'Working Schedules',

    'navigation' => [
        'title' => 'Working Schedules',
        'group' => 'Mfanyakazi',
    ],

    'groups' => [
        'status'     => 'Hali',
        'created-by' => 'Imeundwa Na',
        'created-at' => 'Imeundwa',
        'updated-at' => 'Imesasishwa',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Jumla Taarifa',
                'fields' => [
                    'name'                  => 'Jina',
                    'schedule-name'         => 'Ratiba Jina',
                    'schedule-name-tooltip' => 'Tafadhali kuandika descriptive working ratiba jina.',
                    'timezone'              => 'Timezone',
                    'timezone-tooltip'      => 'Tafadhali chagua timezone kwa working ratiba.',
                    'company'               => 'Kampuni',
                ],
            ],

            'configuration' => [
                'title'  => 'Work Saa Usanidi',
                'fields' => [
                    'hours-per-day'                   => 'Saa Kwa Siku',
                    'hours-per-day-suffix'            => 'Saa',
                    'full-time-required-hours'        => 'Kamili Time Inahitajika Saa',
                    'full-time-required-hours-suffix' => 'Saa Kwa Wiki',
                ],
            ],

            'flexibility' => [
                'title'  => 'Flexibility',
                'fields' => [
                    'status'                     => 'Hali',
                    'two-weeks-calendar'         => 'Two Weeks Kalenda',
                    'two-weeks-calendar-tooltip' => 'Washa alternating two-wiki work ratiba.',
                    'flexible-hours'             => 'Flexible Saa',
                    'flexible-hours-tooltip'     => 'Ruhusu wafanyakazi hadi ina flexible work saa.',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'id'             => 'Kitambulisho',
            'name'           => 'Ratiba Jina',
            'timezone'       => 'Timezone',
            'company'        => 'Kampuni',
            'flexible-hours' => 'Flexible Saa',
            'status'         => 'Hali',
            'daily-hours'    => 'Kila siku Saa',
            'created-by'     => 'Imeundwa Na',
            'created-at'     => 'Imeundwa',
            'updated-at'     => 'Imesasishwa',
        ],

        'filters' => [
            'company'           => 'Kampuni',
            'is-active'         => 'Hali',
            'two-week-calendar' => 'Two Weeks Kalenda',
            'flexible-hours'    => 'Flexible Saa',
            'timezone'          => 'Timezone',
            'name'              => 'Ratiba Jina',
            'attendance'        => 'Mahudhurio',
            'created-by'        => 'Imeundwa Na',
            'daily-hours'       => 'Kila siku Saa',
            'updated-at'        => 'Imesasishwa',
            'created-at'        => 'Imeundwa',
        ],

        'groups' => [
            'name'           => 'Ratiba Jina',
            'status'         => 'Hali',
            'timezone'       => 'Timezone',
            'flexible-hours' => 'Flexible Saa',
            'daily-hours'    => 'Kila siku Saa',
            'created-by'     => 'Imeundwa Na',
            'created-at'     => 'Imeundwa',
            'updated-at'     => 'Imesasishwa',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Kalenda Plan restored',
                    'body'  => 'kalenda plan ina restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Kalenda Plan imefutwa',
                    'body'  => 'kalenda plan ina imefutwa successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Kalenda Plan force imefutwa',
                    'body'  => 'kalenda plan ina force imefutwa successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Kalenda Plans restored',
                    'body'  => 'kalenda plans ina restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Kalenda Plans imefutwa',
                    'body'  => 'kalenda plans ina imefutwa successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Kalenda Plans force imefutwa',
                    'body'  => 'kalenda plans ina force imefutwa successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'   => 'Jumla Taarifa',
                'entries' => [
                    'name'                  => 'Jina',
                    'schedule-name'         => 'Ratiba Jina',
                    'schedule-name-tooltip' => 'Tafadhali kuandika descriptive working ratiba jina.',
                    'timezone'              => 'Timezone',
                    'timezone-tooltip'      => 'Tafadhali chagua timezone kwa working ratiba.',
                    'company'               => 'Kampuni',
                ],
            ],

            'configuration' => [
                'title'   => 'Work Saa Usanidi',
                'entries' => [
                    'hours-per-day'                   => 'Saa Kwa Siku',
                    'hours-per-day-suffix'            => ' Saa',
                    'full-time-required-hours'        => 'Kamili Time Inahitajika Saa',
                    'full-time-required-hours-suffix' => ' Saa Kwa Wiki',
                ],
            ],

            'flexibility' => [
                'title'   => 'Flexibility',
                'entries' => [
                    'status'                     => 'Hali',
                    'two-weeks-calendar'         => 'Two Weeks Kalenda',
                    'two-weeks-calendar-tooltip' => 'Washa alternating two-wiki work ratiba.',
                    'flexible-hours'             => 'Flexible Saa',
                    'flexible-hours-tooltip'     => 'Ruhusu wafanyakazi hadi ina flexible work saa.',
                ],
            ],
        ],
    ],
];
