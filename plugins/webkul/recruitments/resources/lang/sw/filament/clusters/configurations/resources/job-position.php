<?php

return [
    'navigation' => [
        'title' => 'Nafasi za kazi',
        'group' => 'Nafasi za kazi',
    ],

    'global-search' => [
        'name'            => 'Nafasi ya kazi',
        'department'      => 'Idara',
        'employment-type' => 'Aina ya ajira',
        'company'         => 'Kampuni',
        'created-by'      => 'Imeundwa na',
    ],

    'form' => [
        'sections' => [
            'employment-information' => [
                'title' => 'Taarifa za ajira',

                'fields' => [
                    'job-position-title'         => 'Jina la nafasi ya kazi',
                    'job-position-title-tooltip' => 'Weka jina rasmi la nafasi ya kazi',
                    'department'                 => 'Idara',
                    'department-modal-title'     => 'Unda idara',
                    'job-location'               => 'Mahali pa kazi',
                    'industry'                   => 'Sekta',
                    'company'                    => 'Kampuni',
                    'employment-type'            => 'Aina ya ajira',
                    'interviewers'               => 'Wasaili',
                    'recruiter'                  => 'Afisa uajiri',
                    'manager'                    => 'Meneja',
                    'company-modal-title'        => 'Unda kampuni',
                ],
            ],

            'job-description' => [
                'title' => 'Maelezo ya kazi',

                'fields' => [
                    'job-description'  => 'Maelezo ya kazi',
                    'job-requirements' => 'Mahitaji ya kazi',
                ],
            ],

            'workforce-planning' => [
                'title' => 'Mipango ya wafanyakazi',

                'fields' => [
                    'recruitment-target' => 'Lengo la uajiri',
                    'date-from'          => 'Kuanzia tarehe',
                    'date-to'            => 'Hadi tarehe',
                    'expected-skills'    => 'Ujuzi unaotarajiwa',
                    'employment-type'    => 'Aina ya ajira',
                    'status'             => 'Hali',
                ],
            ],

            'position-status' => [
                'title' => 'Hali ya nafasi',

                'fields' => [
                    'status' => 'Hali',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'id'                 => 'ID',
            'name'               => 'Nafasi ya kazi',
            'department'         => 'Idara',
            'job-position'       => 'Nafasi ya kazi',
            'company'            => 'Kampuni',
            'expected-employees' => 'Wafanyakazi wanaotarajiwa',
            'current-employees'  => 'Wafanyakazi waliopo',
            'status'             => 'Hali',
            'created-by'         => 'Imeundwa na',
            'created-at'         => 'Tarehe ya kuundwa',
            'updated-at'         => 'Tarehe ya kusasishwa',
        ],

        'filters' => [
            'department'      => 'Idara',
            'employment-type' => 'Aina ya ajira',
            'job-position'    => 'Nafasi ya kazi',
            'company'         => 'Kampuni',
            'status'          => 'Hali',
            'created-by'      => 'Imeundwa na',
            'updated-at'      => 'Tarehe ya kusasishwa',
            'created-at'      => 'Tarehe ya kuundwa',
        ],

        'groups' => [
            'job-position'    => 'Nafasi ya kazi',
            'company'         => 'Kampuni',
            'department'      => 'Idara',
            'employment-type' => 'Aina ya ajira',
            'created-by'      => 'Imeundwa na',
            'created-at'      => 'Tarehe ya kuundwa',
            'updated-at'      => 'Tarehe ya kusasishwa',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Nafasi ya kazi imerejeshwa',
                    'body'  => 'Nafasi ya kazi imerejeshwa kwa mafanikio.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Nafasi ya kazi imefutwa',
                    'body'  => 'Nafasi ya kazi imefutwa kwa mafanikio.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Nafasi za kazi zimerejeshwa',
                    'body'  => 'Nafasi za kazi zimerejeshwa kwa mafanikio.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Nafasi za kazi zimefutwa',
                    'body'  => 'Nafasi za kazi zimefutwa kwa mafanikio.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Nafasi za kazi zimefutwa kabisa',
                    'body'  => 'Nafasi za kazi zimefutwa kabisa.',
                ],
            ],
        ],

        'empty-state-actions' => [
            'create' => [
                'notification' => [
                    'title' => 'Nafasi ya kazi imeundwa',
                    'body'  => 'Nafasi ya kazi imeundwa kwa mafanikio.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'employment-information' => [
                'title' => 'Taarifa za ajira',

                'entries' => [
                    'job-position-title' => 'Jina la nafasi ya kazi',
                    'department'         => 'Idara',
                    'company'            => 'Kampuni',
                    'employment-type'    => 'Aina ya ajira',
                    'job-location'       => 'Mahali pa kazi',
                    'industry'           => 'Sekta',
                    'manager'            => 'Meneja',
                    'recruiter'          => 'Afisa uajiri',
                    'interviewers'       => 'Wasaili',
                ],
            ],
            'job-description' => [
                'title' => 'Maelezo ya kazi',

                'entries' => [
                    'job-description'  => 'Maelezo ya kazi',
                    'job-requirements' => 'Mahitaji ya kazi',
                ],
            ],
            'work-planning' => [
                'title' => 'Mipango ya wafanyakazi',

                'entries' => [
                    'expected-employees' => 'Wafanyakazi wanaotarajiwa',
                    'current-employees'  => 'Wafanyakazi waliopo',
                    'date-from'          => 'Kuanzia tarehe',
                    'date-to'            => 'Hadi tarehe',
                    'recruitment-target' => 'Lengo la uajiri',
                ],
            ],
            'position-status' => [
                'title' => 'Hali ya nafasi',

                'entries' => [
                    'status' => 'Hali',
                ],
            ],
        ],
    ],
];
