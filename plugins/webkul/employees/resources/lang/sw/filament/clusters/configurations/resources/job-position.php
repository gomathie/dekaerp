<?php

return [
    'title' => 'Vyeo vya kazi',

    'navigation' => [
        'title' => 'Vyeo vya kazi',
        'group' => 'Uajiri',
    ],

    'form' => [
        'sections' => [
            'employment-information' => [
                'title' => 'Taarifa za ajira',

                'fields' => [
                    'job-position-title'         => 'Jina la cheo cha kazi',
                    'job-position-title-tooltip' => 'Weka jina rasmi la cheo cha kazi',
                    'department'                 => 'Idara',
                    'department-modal-title'     => 'Unda idara',
                    'company-modal-title'        => 'Unda kampuni',
                    'job-location'               => 'Mahali pa kazi',
                    'industry'                   => 'Sekta',
                    'company'                    => 'Kampuni',
                    'employment-type'            => 'Aina ya ajira',
                    'recruiter'                  => 'Afisa uajiri',
                    'interviewer'                => 'Msaili',
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
                    'recruitment-target'         => 'Lengo la uajiri',
                    'date-from'                  => 'Tarehe kuanzia',
                    'date-to'                    => 'Tarehe hadi',
                    'expected-skills'            => 'Ujuzi unaotarajiwa',
                    'employment-type'            => 'Aina ya ajira',
                    'status'                     => 'Hali',
                ],
            ],

            'position-status' => [
                'title' => 'Hali ya cheo',

                'fields' => [
                    'status' => 'Hali',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'id'                 => 'ID',
            'name'               => 'Cheo cha kazi',
            'department'         => 'Idara',
            'job-position'       => 'Cheo cha kazi',
            'company'            => 'Kampuni',
            'expected-employees' => 'Wafanyakazi wanaotarajiwa',
            'current-employees'  => 'Wafanyakazi wa sasa',
            'status'             => 'Hali',
            'created-by'         => 'Imeundwa na',
            'created-at'         => 'Imeundwa',
            'updated-at'         => 'Imesasishwa',
        ],

        'filters' => [
            'department'      => 'Idara',
            'employment-type' => 'Aina ya ajira',
            'job-position'    => 'Cheo cha kazi',
            'company'         => 'Kampuni',
            'status'          => 'Hali',
            'created-by'      => 'Imeundwa na',
            'updated-at'      => 'Imesasishwa',
            'created-at'      => 'Imeundwa',
        ],

        'groups' => [
            'job-position'    => 'Cheo cha kazi',
            'company'         => 'Kampuni',
            'department'      => 'Idara',
            'employment-type' => 'Aina ya ajira',
            'created-by'      => 'Imeundwa na',
            'created-at'      => 'Imeundwa',
            'updated-at'      => 'Imesasishwa',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Cheo cha kazi kimerejeshwa',
                    'body'  => 'Cheo cha kazi kimerejeshwa kikamilifu.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Cheo cha kazi kimefutwa',
                    'body'  => 'Cheo cha kazi kimefutwa kikamilifu.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Vyeo vya kazi vimerejeshwa',
                    'body'  => 'Vyeo vya kazi vimerejeshwa kikamilifu.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Vyeo vya kazi vimefutwa',
                    'body'  => 'Vyeo vya kazi vimefutwa kikamilifu.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Vyeo vya kazi vimefutwa kabisa',
                    'body'  => 'Vyeo vya kazi vimefutwa kabisa.',
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
                    'job-position-title' => 'Jina la cheo cha kazi',
                    'department'         => 'Idara',
                    'company'            => 'Kampuni',
                    'employment-type'    => 'Aina ya ajira',
                    'job-location'       => 'Mahali pa kazi',
                    'industry'           => 'Sekta',
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
                    'current-employees'  => 'Wafanyakazi wa sasa',
                    'date-from'          => 'Tarehe kuanzia',
                    'date-to'            => 'Tarehe hadi',
                    'recruitment-target' => 'Lengo la uajiri',
                ],
            ],
            'position-status' => [
                'title' => 'Hali ya cheo',

                'entries' => [
                    'status' => 'Hali',
                ],
            ],
        ],
    ],
];
