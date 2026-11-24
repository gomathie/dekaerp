<?php

return [
    'title' => 'Mwombaji',

    'navigation' => [
        'title' => 'Waombaji',
    ],

    'global-search' => [
        'department' => 'Idara',
        'work-email' => 'Barua pepe ya kazini',
        'work-phone' => 'Simu ya kazini',
    ],

    'form' => [
        'sections' => [
            'general-information' => [
                'title' => 'Taarifa za jumla',

                'fields' => [
                    'evaluation-good'           => 'Tathmini: Nzuri',
                    'evaluation-very-good'      => 'Tathmini: Nzuri sana',
                    'evaluation-very-excellent' => 'Tathmini: Bora kabisa',
                    'hired'                     => 'Ameajiriwa',
                    'candidate-name'            => 'Jina la mgombea',
                    'email'                     => 'Barua pepe',
                    'phone'                     => 'Simu',
                    'linkedin-profile'          => 'Wasifu wa LinkedIn',
                    'recruiter'                 => 'Afisa uajiri',
                    'interviewer'               => 'Msaili',
                    'tags'                      => 'Lebo',
                    'notes'                     => 'Vidokezo',
                    'hired-date'                => 'Tarehe ya kuajiriwa',
                    'job-position'              => 'Nafasi za kazi',
                ],
            ],

            'education-and-availability' => [
                'title' => 'Elimu na upatikanaji',

                'fields' => [
                    'degree'            => 'Shahada',
                    'availability-date' => 'Tarehe ya kupatikana',
                ],
            ],

            'department' => [
                'title' => 'Idara',
            ],

            'salary' => [
                'title' => 'Mshahara unaotarajiwa na unaopendekezwa',

                'fields' => [
                    'expected-salary'       => 'Mshahara unaotarajiwa',
                    'salary-proposed-extra' => 'Marupurupu mengine',
                    'proposed-salary'       => 'Mshahara unaopendekezwa',
                    'salary-expected-extra' => 'Marupurupu mengine',
                ],
            ],

            'source-and-medium' => [
                'title' => 'Chanzo na njia',

                'fields' => [
                    'source' => 'Chanzo',
                    'medium' => 'Njia',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'partner-name'       => 'Jina la mhusika',
            'applied-on'         => 'Tarehe ya kuomba',
            'job-position'       => 'Nafasi ya kazi',
            'stage'              => 'Hatua',
            'candidate-name'     => 'Jina la mgombea',
            'evaluation'         => 'Tathmini',
            'application-status' => 'Hali ya ombi',
            'tags'               => 'Lebo',
            'refuse-reason'      => 'Sababu ya kukataa',
            'email'              => 'Barua pepe',
            'recruiter'          => 'Afisa uajiri',
            'interviewer'        => 'Msaili',
            'candidate-phone'    => 'Simu',
            'medium'             => 'Njia',
            'source'             => 'Chanzo',
            'salary-expected'    => 'Mshahara unaotarajiwa',
            'availability-date'  => 'Tarehe ya kupatikana',
        ],

        'filters' => [
            'source'                  => 'Chanzo',
            'medium'                  => 'Njia',
            'candidate'               => 'Mgombea',
            'priority'                => 'Kipaumbele',
            'salary-proposed-extra'   => 'Marupurupu yanayopendekezwa',
            'salary-expected-extra'   => 'Marupurupu yanayotarajiwa',
            'applicant-notes'         => 'Vidokezo vya mwombaji',
            'create-date'             => 'Tarehe ya kuomba',
            'date-closed'             => 'Tarehe ya kuajiriwa',
            'date-last-stage-updated' => 'Hatua ilibadilishwa mwisho',
            'stage'                   => 'Hatua',
            'job-position'            => 'Nafasi ya kazi',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Mwombaji amefutwa',
                    'body'  => 'Mwombaji amefutwa kwa mafanikio.',
                ],
            ],
        ],

        'groups' => [
            'stage'          => 'Hatua',
            'job-position'   => 'Nafasi ya kazi',
            'candidate-name' => 'Jina la mgombea',
            'responsible'    => 'Msimamizi',
            'creation-date'  => 'Tarehe ya kuundwa',
            'hired-date'     => 'Tarehe ya kuajiriwa',
            'last-stage'     => 'Hatua ya mwisho',
            'refuse-reason'  => 'Sababu ya kukataa',
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Wafanyakazi wamefutwa',
                    'body'  => 'Wafanyakazi wamefutwa kwa mafanikio.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Wafanyakazi wamefutwa',
                    'body'  => 'Wafanyakazi wamefutwa kwa mafanikio.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Wafanyakazi wamerejeshwa',
                    'body'  => 'Wafanyakazi wamerejeshwa kwa mafanikio.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general-information' => [
                'title' => 'Taarifa za jumla',

                'entries' => [
                    'evaluation-good'           => 'Tathmini: Nzuri',
                    'evaluation-very-good'      => 'Tathmini: Nzuri sana',
                    'evaluation-very-excellent' => 'Tathmini: Bora kabisa',
                    'hired'                     => 'Ameajiriwa',
                    'candidate-name'            => 'Jina la mgombea',
                    'email'                     => 'Barua pepe',
                    'phone'                     => 'Simu',
                    'linkedin-profile'          => 'Wasifu wa LinkedIn',
                    'recruiter'                 => 'Afisa uajiri',
                    'interviewer'               => 'Msaili',
                    'tags'                      => 'Lebo',
                    'notes'                     => 'Vidokezo',
                    'job-position'              => 'Nafasi za kazi',
                ],
            ],

            'education-and-availability' => [
                'title' => 'Elimu na upatikanaji',

                'entries' => [
                    'degree'            => 'Shahada',
                    'availability-date' => 'Tarehe ya kupatikana',
                ],
            ],

            'department' => [
                'title' => 'Idara',
            ],

            'salary' => [
                'title' => 'Mshahara unaotarajiwa na unaopendekezwa',

                'entries' => [
                    'expected-salary'       => 'Mshahara unaotarajiwa',
                    'salary-proposed-extra' => 'Marupurupu mengine',
                    'proposed-salary'       => 'Mshahara unaopendekezwa',
                    'salary-expected-extra' => 'Marupurupu mengine',
                ],
            ],

            'source-and-medium' => [
                'title' => 'Chanzo na njia',

                'entries' => [
                    'source' => 'Chanzo',
                    'medium' => 'Njia',
                ],
            ],
        ],
    ],
];
