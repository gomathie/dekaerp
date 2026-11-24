<?php

return [
    'title' => 'Ratiba za kazi',

    'navigation' => [
        'title' => 'Ratiba za kazi',
        'group' => 'Mfanyakazi',
    ],

    'groups' => [
        'status'     => 'Hali',
        'created-by' => 'Imeundwa na',
        'created-at' => 'Imeundwa',
        'updated-at' => 'Imesasishwa',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Taarifa za jumla',
                'fields' => [
                    'name'                  => 'Jina',
                    'schedule-name'         => 'Jina la ratiba',
                    'schedule-name-tooltip' => 'Andika jina linaloeleza ratiba ya kazi.',
                    'timezone'              => 'Saa za eneo',
                    'timezone-tooltip'      => 'Chagua saa za eneo za ratiba ya kazi.',
                    'company'               => 'Kampuni',
                ],
            ],

            'configuration' => [
                'title'  => 'Mpangilio wa saa za kazi',
                'fields' => [
                    'hours-per-day'                   => 'Saa kwa siku',
                    'hours-per-day-suffix'            => 'Saa',
                    'full-time-required-hours'        => 'Saa zinazohitajika kwa muda wote',
                    'full-time-required-hours-suffix' => 'Saa kwa wiki',
                ],
            ],

            'flexibility' => [
                'title'  => 'Unyumbufu',
                'fields' => [
                    'status'                     => 'Hali',
                    'two-weeks-calendar'         => 'Ratiba ya wiki mbili',
                    'two-weeks-calendar-tooltip' => 'Washa ratiba ya kazi ya wiki mbili zinazopishana.',
                    'flexible-hours'             => 'Saa zinazonyumbulika',
                    'flexible-hours-tooltip'     => 'Ruhusu wafanyakazi kuwa na saa za kazi zinazonyumbulika.',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'id'             => 'ID',
            'name'           => 'Jina la ratiba',
            'timezone'       => 'Saa za eneo',
            'company'        => 'Kampuni',
            'flexible-hours' => 'Saa zinazonyumbulika',
            'status'         => 'Hali',
            'daily-hours'    => 'Saa za kila siku',
            'created-by'     => 'Imeundwa na',
            'created-at'     => 'Imeundwa',
            'updated-at'     => 'Imesasishwa',
        ],

        'filters' => [
            'company'           => 'Kampuni',
            'is-active'         => 'Hali',
            'two-week-calendar' => 'Ratiba ya wiki mbili',
            'flexible-hours'    => 'Saa zinazonyumbulika',
            'timezone'          => 'Saa za eneo',
            'name'              => 'Jina la ratiba',
            'attendance'        => 'Mahudhurio',
            'created-by'        => 'Imeundwa na',
            'daily-hours'       => 'Saa za kila siku',
            'updated-at'        => 'Imesasishwa',
            'created-at'        => 'Imeundwa',
        ],

        'groups' => [
            'name'           => 'Jina la ratiba',
            'status'         => 'Hali',
            'timezone'       => 'Saa za eneo',
            'flexible-hours' => 'Saa zinazonyumbulika',
            'daily-hours'    => 'Saa za kila siku',
            'created-by'     => 'Imeundwa na',
            'created-at'     => 'Imeundwa',
            'updated-at'     => 'Imesasishwa',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Ratiba imerejeshwa',
                    'body'  => 'Ratiba imerejeshwa kikamilifu.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Ratiba imefutwa',
                    'body'  => 'Ratiba imefutwa kikamilifu.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Ratiba imefutwa kabisa',
                    'body'  => 'Ratiba imefutwa kabisa.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Ratiba zimerejeshwa',
                    'body'  => 'Ratiba zimerejeshwa kikamilifu.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Ratiba zimefutwa',
                    'body'  => 'Ratiba zimefutwa kikamilifu.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Ratiba zimefutwa kabisa',
                    'body'  => 'Ratiba zimefutwa kabisa.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'   => 'Taarifa za jumla',
                'entries' => [
                    'name'                  => 'Jina',
                    'schedule-name'         => 'Jina la ratiba',
                    'schedule-name-tooltip' => 'Andika jina linaloeleza ratiba ya kazi.',
                    'timezone'              => 'Saa za eneo',
                    'timezone-tooltip'      => 'Chagua saa za eneo za ratiba ya kazi.',
                    'company'               => 'Kampuni',
                ],
            ],

            'configuration' => [
                'title'   => 'Mpangilio wa saa za kazi',
                'entries' => [
                    'hours-per-day'                   => 'Saa kwa siku',
                    'hours-per-day-suffix'            => ' saa',
                    'full-time-required-hours'        => 'Saa zinazohitajika kwa muda wote',
                    'full-time-required-hours-suffix' => ' saa kwa wiki',
                ],
            ],

            'flexibility' => [
                'title'   => 'Unyumbufu',
                'entries' => [
                    'status'                     => 'Hali',
                    'two-weeks-calendar'         => 'Ratiba ya wiki mbili',
                    'two-weeks-calendar-tooltip' => 'Washa ratiba ya kazi ya wiki mbili zinazopishana.',
                    'flexible-hours'             => 'Saa zinazonyumbulika',
                    'flexible-hours-tooltip'     => 'Ruhusu wafanyakazi kuwa na saa za kazi zinazonyumbulika.',
                ],
            ],
        ],
    ],
];
