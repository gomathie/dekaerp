<?php

return [
    'title' => 'Candidate',

    'navigation' => [
        'title' => 'Candidates',
    ],

    'global-search' => [
        'email-from' => 'Barua pepe Kutoka',
        'phone'      => 'Simu',
        'company'    => 'Kampuni',
        'degree'     => 'Degree',
    ],

    'form' => [
        'sections' => [
            'basic-information' => [
                'title' => 'Basic Taarifa',

                'fields' => [
                    'full-name' => 'Kamili Jina',
                    'email'     => 'Barua pepe Anwani',
                    'phone'     => 'Simu Nambari',
                    'linkedin'  => 'LinkedIn Wasifu',
                    'contact'   => 'Mwasiliani',
                ],
            ],

            'additional-details' => [
                'title' => 'Additional Maelezo',

                'fields' => [
                    'company'           => 'Kampuni',
                    'degree'            => 'Degree',
                    'tags'              => 'Lebo',
                    'manager'           => 'Meneja',
                    'availability-date' => 'Availability Tarehe',

                    'priority-options' => [
                        'low'    => 'Chini',
                        'medium' => 'Wastani',
                        'high'   => 'Juu',
                    ],
                ],
            ],

            'status-and-evaluation' => [
                'title' => 'Hali',

                'fields' => [
                    'active'     => 'Inatumika',
                    'evaluation' => 'Evaluation',
                ],
            ],

            'communication' => [
                'title' => 'Mawasiliano',

                'fields' => [
                    'cc-email'      => 'CC Barua pepe',
                    'email-bounced' => 'Barua pepe Bounced',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'       => 'Kamili Jina',
            'tags'       => 'Lebo',
            'evaluation' => 'Evaluation',
        ],

        'filters' => [
            'company'      => 'Kampuni',
            'partner-name' => 'Mwasiliani',
            'degree'       => 'Degree',
            'manager-name' => 'Meneja',
        ],

        'groups' => [
            'manager-name' => 'Meneja',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Candidate Imefutwa',
                    'body'  => 'candidates ina imefutwa successfully.',
                ],
            ],

            'empty-state-actions' => [
                'create' => [
                    'notification' => [
                        'title' => 'Candidate Imeundwa',
                        'body'  => 'candidates ina imeundwa successfully.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Candidates Imefutwa',
                    'body'  => 'candidates ina imefutwa successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'basic-information' => [
                'title' => 'Basic Taarifa',

                'entries' => [
                    'full-name' => 'Kamili Jina',
                    'email'     => 'Barua pepe Anwani',
                    'phone'     => 'Simu Nambari',
                    'linkedin'  => 'LinkedIn Wasifu',
                    'contact'   => 'Mwasiliani',
                ],
            ],

            'additional-details' => [
                'title' => 'Additional Maelezo',

                'entries' => [
                    'company'           => 'Kampuni',
                    'degree'            => 'Degree',
                    'tags'              => 'Lebo',
                    'manager'           => 'Meneja',
                    'availability-date' => 'Availability Tarehe',

                    'priority-options' => [
                        'low'    => 'Chini',
                        'medium' => 'Wastani',
                        'high'   => 'Juu',
                    ],
                ],
            ],

            'status-and-evaluation' => [
                'title' => 'Hali',

                'entries' => [
                    'active'     => 'Inatumika',
                    'evaluation' => 'Evaluation',
                ],
            ],

            'communication' => [
                'title' => 'Mawasiliano',

                'entries' => [
                    'cc-email'      => 'CC Barua pepe',
                    'email-bounced' => 'Barua pepe Bounced',
                ],
            ],
        ],
    ],
];
