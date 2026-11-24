<?php

return [
    'title' => 'Mgombea',

    'navigation' => [
        'title' => 'Wagombea',
    ],

    'global-search' => [
        'email-from' => 'Barua pepe kutoka',
        'phone'      => 'Simu',
        'company'    => 'Kampuni',
        'degree'     => 'Shahada',
    ],

    'form' => [
        'sections' => [
            'basic-information' => [
                'title' => 'Taarifa za msingi',

                'fields' => [
                    'full-name' => 'Jina kamili',
                    'email'     => 'Anwani ya barua pepe',
                    'phone'     => 'Namba ya simu',
                    'linkedin'  => 'Wasifu wa LinkedIn',
                    'contact'   => 'Mhusika',
                ],
            ],

            'additional-details' => [
                'title' => 'Maelezo ya ziada',

                'fields' => [
                    'company'           => 'Kampuni',
                    'degree'            => 'Shahada',
                    'tags'              => 'Lebo',
                    'manager'           => 'Meneja',
                    'availability-date' => 'Tarehe ya kupatikana',

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
                    'active'     => 'Hai',
                    'evaluation' => 'Tathmini',
                ],
            ],

            'communication' => [
                'title' => 'Mawasiliano',

                'fields' => [
                    'cc-email'      => 'Nakala ya barua pepe',
                    'email-bounced' => 'Barua pepe imerudishwa',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'       => 'Jina kamili',
            'tags'       => 'Lebo',
            'evaluation' => 'Tathmini',
        ],

        'filters' => [
            'company'      => 'Kampuni',
            'partner-name' => 'Mhusika',
            'degree'       => 'Shahada',
            'manager-name' => 'Meneja',
        ],

        'groups' => [
            'manager-name' => 'Meneja',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Mgombea amefutwa',
                    'body'  => 'Wagombea wamefutwa kwa mafanikio.',
                ],
            ],

            'empty-state-actions' => [
                'create' => [
                    'notification' => [
                        'title' => 'Mgombea ameundwa',
                        'body'  => 'Mgombea ameundwa kwa mafanikio.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Wagombea wamefutwa',
                    'body'  => 'Wagombea wamefutwa kwa mafanikio.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'basic-information' => [
                'title' => 'Taarifa za msingi',

                'entries' => [
                    'full-name' => 'Jina kamili',
                    'email'     => 'Anwani ya barua pepe',
                    'phone'     => 'Namba ya simu',
                    'linkedin'  => 'Wasifu wa LinkedIn',
                    'contact'   => 'Mhusika',
                ],
            ],

            'additional-details' => [
                'title' => 'Maelezo ya ziada',

                'entries' => [
                    'company'           => 'Kampuni',
                    'degree'            => 'Shahada',
                    'tags'              => 'Lebo',
                    'manager'           => 'Meneja',
                    'availability-date' => 'Tarehe ya kupatikana',

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
                    'active'     => 'Hai',
                    'evaluation' => 'Tathmini',
                ],
            ],

            'communication' => [
                'title' => 'Mawasiliano',

                'entries' => [
                    'cc-email'      => 'Nakala ya barua pepe',
                    'email-bounced' => 'Barua pepe imerudishwa',
                ],
            ],
        ],
    ],
];
