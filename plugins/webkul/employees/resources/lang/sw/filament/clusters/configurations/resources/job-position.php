<?php

return [
    'title' => 'Kazi Positions',

    'navigation' => [
        'title' => 'Kazi Positions',
        'group' => 'Ajira',
    ],

    'form' => [
        'sections' => [
            'employment-information' => [
                'title' => 'Employment Taarifa',

                'fields' => [
                    'job-position-title'         => 'Kazi Cheo Kichwa',
                    'job-position-title-tooltip' => 'Ingiza official kazi cheo kichwa',
                    'department'                 => 'Idara',
                    'department-modal-title'     => 'Unda Idara',
                    'company-modal-title'        => 'Unda Kampuni',
                    'job-location'               => 'Kazi Mahali',
                    'industry'                   => 'Industry',
                    'company'                    => 'Kampuni',
                    'employment-type'            => 'Employment Aina',
                    'recruiter'                  => 'Recruiter',
                    'interviewer'                => 'Interviewer',
                ],
            ],

            'job-description' => [
                'title' => 'Kazi Maelezo',

                'fields' => [
                    'job-description'  => 'Kazi Maelezo',
                    'job-requirements' => 'Kazi Requirements',
                ],
            ],

            'workforce-planning' => [
                'title' => 'Workforce Planning',

                'fields' => [
                    'recruitment-target'         => 'Ajira Lengwa',
                    'date-from'                  => 'Tarehe Kutoka',
                    'date-to'                    => 'Tarehe Hadi',
                    'expected-skills'            => 'Inatarajiwa Skills',
                    'employment-type'            => 'Employment Aina',
                    'status'                     => 'Hali',
                ],
            ],

            'position-status' => [
                'title' => 'Cheo Hali',

                'fields' => [
                    'status' => 'Hali',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'id'                 => 'Kitambulisho',
            'name'               => 'Kazi Cheo',
            'department'         => 'Idara',
            'job-position'       => 'Kazi Cheo',
            'company'            => 'Kampuni',
            'expected-employees' => 'Inatarajiwa Wafanyakazi',
            'current-employees'  => 'Sasa Wafanyakazi',
            'status'             => 'Hali',
            'created-by'         => 'Imeundwa Na',
            'created-at'         => 'Imeundwa',
            'updated-at'         => 'Imesasishwa',
        ],

        'filters' => [
            'department'      => 'Idara',
            'employment-type' => 'Employment Aina',
            'job-position'    => 'Kazi Cheo',
            'company'         => 'Kampuni',
            'status'          => 'Hali',
            'created-by'      => 'Imeundwa Na',
            'updated-at'      => 'Imesasishwa',
            'created-at'      => 'Imeundwa',
        ],

        'groups' => [
            'job-position'    => 'Kazi Cheo',
            'company'         => 'Kampuni',
            'department'      => 'Idara',
            'employment-type' => 'Employment Aina',
            'created-by'      => 'Imeundwa Na',
            'created-at'      => 'Imeundwa',
            'updated-at'      => 'Imesasishwa',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Kazi Cheo restored',
                    'body'  => 'Kazi Cheo ina restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Kazi Cheo imefutwa',
                    'body'  => 'Kazi Cheo ina imefutwa successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Kazi Positions restored',
                    'body'  => 'Kazi Positions ina restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Kazi Positions imefutwa',
                    'body'  => 'Kazi Positions ina imefutwa successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Kazi Positions force imefutwa',
                    'body'  => 'Kazi Positions ina force imefutwa successfully.',
                ],
            ],
        ],

        'empty-state-actions' => [
            'create' => [
                'notification' => [
                    'title' => 'Kazi Positions',
                    'body'  => 'Kazi Positions ina imeundwa successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'employment-information' => [
                'title' => 'Employment Taarifa',

                'entries' => [
                    'job-position-title' => 'Kazi Cheo Kichwa',
                    'department'         => 'Idara',
                    'company'            => 'Kampuni',
                    'employment-type'    => 'Employment Aina',
                    'job-location'       => 'Kazi Mahali',
                    'industry'           => 'Industry',
                ],
            ],
            'job-description' => [
                'title' => 'Kazi Maelezo',

                'entries' => [
                    'job-description'  => 'Kazi Maelezo',
                    'job-requirements' => 'Kazi Requirements',
                ],
            ],
            'work-planning' => [
                'title' => 'Workforce Planning',

                'entries' => [
                    'expected-employees' => 'Inatarajiwa Wafanyakazi',
                    'current-employees'  => 'Sasa Wafanyakazi',
                    'date-from'          => 'Tarehe Kutoka',
                    'date-to'            => 'Tarehe Hadi',
                    'recruitment-target' => 'Ajira Lengwa',
                ],
            ],
            'position-status' => [
                'title' => 'Cheo Hali',

                'entries' => [
                    'status' => 'Hali',
                ],
            ],
        ],
    ],
];
