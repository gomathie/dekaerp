<?php

return [
    'navigation' => [
        'title' => 'Miradi',
    ],

    'global-search' => [
        'project-manager' => 'Meneja wa mradi',
        'customer'        => 'Mteja',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Jumla',

                'fields' => [
                    'name'             => 'Jina',
                    'name-placeholder' => 'Jina la mradi...',
                    'description'      => 'Maelezo',
                ],
            ],

            'additional' => [
                'title' => 'Taarifa za ziada',

                'fields' => [
                    'project-manager'             => 'Meneja wa mradi',
                    'customer'                    => 'Mteja',
                    'start-date'                  => 'Tarehe ya kuanza',
                    'end-date'                    => 'Tarehe ya kumaliza',
                    'allocated-hours'             => 'Saa zilizotengwa',
                    'allocated-hours-helper-text' => 'Kwa saa (mf. saa 1.5 ni saa 1 na dakika 30)',
                    'tags'                        => 'Lebo',
                    'company'                     => 'Kampuni',
                ],
            ],

            'settings' => [
                'title' => 'Mipangilio',

                'fields' => [
                    'visibility'                   => 'Mwonekano',
                    'visibility-hint-tooltip'      => 'Wape wafanyakazi ufikiaji wa mradi au kazi zako kwa kuwaongeza kama wafuatiliaji. Watapata ufikiaji wa kazi zote walizopewa moja kwa moja.',
                    'private-description'          => 'Watumiaji wa ndani walioalikwa pekee.',
                    'internal-description'         => 'Watumiaji wote wa ndani wanaweza kuona.',
                    'public-description'           => 'Watumiaji wa portal walioalikwa na watumiaji wote wa ndani.',
                    'time-management'              => 'Usimamizi wa muda',
                    'allow-timesheets'             => 'Ruhusu rejista za muda',
                    'allow-timesheets-helper-text' => 'Rekodi muda kwenye kazi na ufuatilie maendeleo',
                    'task-management'              => 'Usimamizi wa kazi',
                    'allow-milestones'             => 'Ruhusu hatua muhimu',
                    'allow-milestones-helper-text' => 'Fuatilia hatua muhimu zinazohitajika ili kufanikiwa.',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'            => 'Jina',
            'customer'        => 'Mteja',
            'start-date'      => 'Tarehe ya kuanza',
            'end-date'        => 'Tarehe ya kumaliza',
            'planned-date'    => 'Tarehe iliyopangwa',
            'remaining-hours' => 'Saa zilizobaki',
            'project-manager' => 'Meneja wa mradi',
        ],

        'groups' => [
            'stage'           => 'Hatua',
            'project-manager' => 'Meneja wa mradi',
            'customer'        => 'Mteja',
            'created-at'      => 'Tarehe ya kuundwa',
        ],

        'filters' => [
            'name'             => 'Jina',
            'visibility'       => 'Mwonekano',
            'start-date'       => 'Tarehe ya kuanza',
            'end-date'         => 'Tarehe ya kumaliza',
            'allow-timesheets' => 'Ruhusu rejista za muda',
            'allow-milestones' => 'Ruhusu hatua muhimu',
            'allocated-hours'  => 'Saa zilizotengwa',
            'created-at'       => 'Tarehe ya kuundwa',
            'updated-at'       => 'Tarehe ya kusasishwa',
            'stage'            => 'Hatua',
            'customer'         => 'Mteja',
            'project-manager'  => 'Meneja wa mradi',
            'company'          => 'Kampuni',
            'creator'          => 'Aliyeunda',
            'tags'             => 'Lebo',
        ],

        'actions' => [
            'tasks'      => 'Kazi: :count',
            'milestones' => 'Hatua muhimu :completed kati ya :all zimekamilika',

            'restore' => [
                'notification' => [
                    'title' => 'Mradi umerejeshwa',
                    'body'  => 'Mradi umerejeshwa kwa mafanikio.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Mradi umefutwa',
                    'body'  => 'Mradi umefutwa kwa mafanikio.',
                ],
            ],

            'force-delete' => [

                'notification' => [

                    'success' => [
                        'title' => 'Mradi umefutwa kabisa',
                        'body'  => 'Mradi umefutwa kabisa.',
                    ],

                    'error' => [
                        'title' => 'Mradi hauwezi kufutwa kabisa',
                        'body'  => 'Mradi unahusishwa na rekodi nyingine.',
                    ],

                ],
            ],

        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Jumla',

                'entries' => [
                    'name'             => 'Jina',
                    'name-placeholder' => 'Jina la mradi...',
                    'description'      => 'Maelezo',
                ],
            ],

            'additional' => [
                'title' => 'Taarifa za ziada',

                'entries' => [
                    'project-manager'        => 'Meneja wa mradi',
                    'customer'               => 'Mteja',
                    'project-timeline'       => 'Ratiba ya mradi',
                    'allocated-hours'        => 'Saa zilizotengwa',
                    'allocated-hours-suffix' => ' saa',
                    'remaining-hours'        => 'Saa zilizobaki',
                    'remaining-hours-suffix' => ' saa',
                    'current-stage'          => 'Hatua ya sasa',
                    'tags'                   => 'Lebo',
                ],
            ],

            'statistics' => [
                'title' => 'Takwimu',

                'entries' => [
                    'total-tasks'         => 'Jumla ya kazi',
                    'milestones-progress' => 'Maendeleo ya hatua muhimu',
                ],
            ],

            'record-information' => [
                'title' => 'Taarifa za rekodi',

                'entries' => [
                    'created-at'   => 'Tarehe ya kuundwa',
                    'created-by'   => 'Imeundwa na',
                    'last-updated' => 'Ilisasishwa mwisho',
                ],
            ],

            'settings' => [
                'title' => 'Mipangilio ya mradi',

                'entries' => [
                    'visibility'         => 'Mwonekano',
                    'timesheets-enabled' => 'Rejista za muda zimewashwa',
                    'milestones-enabled' => 'Hatua muhimu zimewashwa',
                ],
            ],
        ],
    ],
];
