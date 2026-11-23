<?php

return [
    'title' => 'Mwombaji',

    'navigation' => [
        'title' => 'Waombaji',
    ],

    'global-search' => [
        'department' => 'Idara',
        'work-email' => 'Work Barua pepe',
        'work-phone' => 'Work Simu',
    ],

    'form' => [
        'sections' => [
            'general-information' => [
                'title' => 'Jumla Taarifa',

                'fields' => [
                    'evaluation-good'           => 'Evaluation: Good',
                    'evaluation-very-good'      => 'Evaluation: Sana Good',
                    'evaluation-very-excellent' => 'Evaluation: Sana Excellent',
                    'hired'                     => 'Hired',
                    'candidate-name'            => 'Candidate jina',
                    'email'                     => 'Emails',
                    'phone'                     => 'Simu',
                    'linkedin-profile'          => 'Linkedin Wasifu',
                    'recruiter'                 => 'Recruiter',
                    'interviewer'               => 'Interviewer',
                    'tags'                      => 'Lebo',
                    'notes'                     => 'Maelezo',
                    'hired-date'                => 'Hired Tarehe',
                    'job-position'              => 'Kazi Positions',
                ],
            ],

            'education-and-availability' => [
                'title' => 'Education & Availability',

                'fields' => [
                    'degree'            => 'Degree',
                    'availability-date' => 'Availability Tarehe',
                ],
            ],

            'department' => [
                'title' => 'Idara',
            ],

            'salary' => [
                'title' => 'Inatarajiwa & Proposed Salary',

                'fields' => [
                    'expected-salary'       => 'Inatarajiwa Salary',
                    'salary-proposed-extra' => 'Nyingine Benefit',
                    'proposed-salary'       => 'Proposed Salary',
                    'salary-expected-extra' => 'Nyingine Benefit',
                ],
            ],

            'source-and-medium' => [
                'title' => 'Chanzo & Wastani',

                'fields' => [
                    'source' => 'Chanzo',
                    'medium' => 'Wastani',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'partner-name'       => 'Mshirika Jina',
            'applied-on'         => 'Imetumika Washa',
            'job-position'       => 'Kazi Cheo',
            'stage'              => 'Hatua',
            'candidate-name'     => 'Candidate Jina',
            'evaluation'         => 'Evaluation',
            'application-status' => 'Application Hali',
            'tags'               => 'Lebo',
            'refuse-reason'      => 'Kataa Sababu',
            'email'              => 'Barua pepe',
            'recruiter'          => 'Recruiter',
            'interviewer'        => 'Interviewer',
            'candidate-phone'    => 'Simu',
            'medium'             => 'Wastani',
            'source'             => 'Chanzo',
            'salary-expected'    => 'Inatarajiwa Salary',
            'availability-date'  => 'Availability Tarehe',
        ],

        'filters' => [
            'source'                  => 'Chanzo',
            'medium'                  => 'Wastani',
            'candidate'               => 'Candidate',
            'priority'                => 'Kipaumbele',
            'salary-proposed-extra'   => 'Salary Proposed Extra',
            'salary-expected-extra'   => 'Salary Inatarajiwa Extra',
            'applicant-notes'         => 'Mwombaji Maelezo',
            'create-date'             => 'Imetumika Washa',
            'date-closed'             => 'Hired Tarehe',
            'date-last-stage-updated' => 'Mwisho Hatua Imesasishwa',
            'stage'                   => 'Hatua',
            'job-position'            => 'Kazi Cheo',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Mwombaji Imefutwa',
                    'body'  => 'mwombaji ilikuwa successfully imefutwa.',
                ],
            ],
        ],

        'groups' => [
            'stage'          => 'Hatua',
            'job-position'   => 'Kazi Cheo',
            'candidate-name' => 'Candidate Jina',
            'responsible'    => 'Responsible',
            'creation-date'  => 'Creation Tarehe',
            'hired-date'     => 'Hired Tarehe',
            'last-stage'     => 'Mwisho Hatua',
            'refuse-reason'  => 'Kataa Sababu',
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Wafanyakazi imefutwa',
                    'body'  => 'wafanyakazi ina imefutwa successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Wafanyakazi imefutwa',
                    'body'  => 'wafanyakazi ina imefutwa successfully.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Wafanyakazi restored',
                    'body'  => 'wafanyakazi ina restored successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general-information' => [
                'title' => 'Jumla Taarifa',

                'entries' => [
                    'evaluation-good'           => 'Evaluation: Good',
                    'evaluation-very-good'      => 'Evaluation: Sana Good',
                    'evaluation-very-excellent' => 'Evaluation: Sana Excellent',
                    'hired'                     => 'Hired',
                    'candidate-name'            => 'Candidate jina',
                    'email'                     => 'Emails',
                    'phone'                     => 'Simu',
                    'linkedin-profile'          => 'Linkedin Wasifu',
                    'recruiter'                 => 'Recruiter',
                    'interviewer'               => 'Interviewer',
                    'tags'                      => 'Lebo',
                    'notes'                     => 'Maelezo',
                    'job-position'              => 'Kazi Positions',
                ],
            ],

            'education-and-availability' => [
                'title' => 'Education & Availability',

                'entries' => [
                    'degree'            => 'Degree',
                    'availability-date' => 'Availability Tarehe',
                ],
            ],

            'department' => [
                'title' => 'Idara',
            ],

            'salary' => [
                'title' => 'Inatarajiwa & Proposed Salary',

                'entries' => [
                    'expected-salary'       => 'Inatarajiwa Salary',
                    'salary-proposed-extra' => 'Nyingine Benefit',
                    'proposed-salary'       => 'Proposed Salary',
                    'salary-expected-extra' => 'Nyingine Benefit',
                ],
            ],

            'source-and-medium' => [
                'title' => 'Chanzo & Wastani',

                'entries' => [
                    'source' => 'Chanzo',
                    'medium' => 'Wastani',
                ],
            ],
        ],
    ],
];
