<?php

return [
    'title' => 'Kazi',

    'navigation' => [
        'title' => 'Kazi',
    ],

    'global-search' => [
        'project'   => 'Mradi',
        'customer'  => 'Mteja',
        'milestone' => 'Hatua muhimu',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Jumla',

                'fields' => [
                    'title'             => 'Kichwa',
                    'title-placeholder' => 'Kichwa cha kazi...',
                    'tags'              => 'Lebo',
                    'name'              => 'Jina',
                    'color'             => 'Rangi',
                    'description'       => 'Maelezo',
                    'project'           => 'Mradi',
                    'status'            => 'Hali',
                    'start_date'        => 'Tarehe ya kuanza',
                    'end_date'          => 'Tarehe ya kumaliza',
                ],
            ],

            'additional' => [
                'title' => 'Taarifa za ziada',
            ],

            'settings' => [
                'title' => 'Mipangilio',

                'fields' => [
                    'project'                     => 'Mradi',
                    'milestone'                   => 'Hatua muhimu',
                    'milestone-hint-text'         => 'Unganisha hatua muhimu na bidhaa ya oda ya mauzo ili huduma zako ziwasilishwe kiotomatiki inapofikiwa.',
                    'name'                        => 'Jina',
                    'deadline'                    => 'Tarehe ya mwisho',
                    'is-completed'                => 'Imekamilika',
                    'customer'                    => 'Mteja',
                    'assignees'                   => 'Waliokabidhiwa',
                    'allocated-hours'             => 'Saa zilizotengwa',
                    'allocated-hours-helper-text' => 'Kwa saa (mf. saa 1.5 ni saa 1 na dakika 30)',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'id'                  => 'ID',
            'priority'            => 'Kipaumbele',
            'state'               => 'Hali',
            'new-state'           => 'Hali mpya',
            'update-state'        => 'Sasisha hali',
            'title'               => 'Kichwa',
            'project'             => 'Mradi',
            'project-placeholder' => 'Kazi binafsi',
            'milestone'           => 'Hatua muhimu',
            'customer'            => 'Mteja',
            'assignees'           => 'Waliokabidhiwa',
            'allocated-time'      => 'Muda uliotengwa',
            'time-spent'          => 'Muda uliotumika',
            'time-remaining'      => 'Muda uliobaki',
            'progress'            => 'Maendeleo',
            'deadline'            => 'Tarehe ya mwisho',
            'tags'                => 'Lebo',
            'stage'               => 'Hatua',
        ],

        'groups' => [
            'state'      => 'Hali',
            'project'    => 'Mradi',
            'milestone'  => 'Hatua muhimu',
            'customer'   => 'Mteja',
            'deadline'   => 'Tarehe ya mwisho',
            'stage'      => 'Hatua',
            'created-at' => 'Tarehe ya kuundwa',
        ],

        'filters' => [
            'title'             => 'Kichwa',
            'priority'          => 'Kipaumbele',
            'low'               => 'Chini',
            'high'              => 'Juu',
            'state'             => 'Hali',
            'tags'              => 'Lebo',
            'allocated-hours'   => 'Saa zilizotengwa',
            'total-hours-spent' => 'Jumla ya saa zilizotumika',
            'remaining-hours'   => 'Saa zilizobaki',
            'overtime'          => 'Muda wa ziada',
            'progress'          => 'Maendeleo',
            'deadline'          => 'Tarehe ya mwisho',
            'created-at'        => 'Tarehe ya kuundwa',
            'updated-at'        => 'Tarehe ya kusasishwa',
            'assignees'         => 'Waliokabidhiwa',
            'customer'          => 'Mteja',
            'project'           => 'Mradi',
            'stage'             => 'Hatua',
            'milestone'         => 'Hatua muhimu',
            'company'           => 'Kampuni',
            'creator'           => 'Aliyeunda',
        ],

        'actions' => [
            'update-state' => [
                'modal-heading' => 'Sasisha hali ya kazi',
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Kazi imerejeshwa',
                    'body'  => 'Kazi imerejeshwa kwa mafanikio.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Kazi imefutwa',
                    'body'  => 'Kazi imefutwa kwa mafanikio.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Kazi imefutwa kabisa',
                    'body'  => 'Kazi imefutwa kabisa.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Kazi zimerejeshwa',
                    'body'  => 'Kazi zimerejeshwa kwa mafanikio.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Kazi zimefutwa',
                    'body'  => 'Kazi zimefutwa kwa mafanikio.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Kazi zimefutwa kabisa',
                    'body'  => 'Kazi zimefutwa kabisa.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Jumla',

                'entries' => [
                    'title'       => 'Kichwa',
                    'state'       => 'Hali',
                    'tags'        => 'Lebo',
                    'priority'    => 'Kipaumbele',
                    'description' => 'Maelezo',
                ],
            ],

            'project-information' => [
                'title' => 'Taarifa za mradi',

                'entries' => [
                    'project'   => 'Mradi',
                    'milestone' => 'Hatua muhimu',
                    'customer'  => 'Mteja',
                    'assignees' => 'Waliokabidhiwa',
                    'deadline'  => 'Tarehe ya mwisho',
                    'stage'     => 'Hatua',
                ],
            ],

            'time-tracking' => [
                'title' => 'Ufuatiliaji wa muda',

                'entries' => [
                    'allocated-time'        => 'Muda uliotengwa',
                    'time-spent'            => 'Muda uliotumika',
                    'time-spent-suffix'     => ' saa',
                    'time-remaining'        => 'Muda uliobaki',
                    'time-remaining-suffix' => ' saa',
                    'progress'              => 'Maendeleo',
                ],
            ],

            'additional-information' => [
                'title' => 'Taarifa za ziada',
            ],

            'record-information' => [
                'title' => 'Taarifa za rekodi',

                'entries' => [
                    'created-at'   => 'Tarehe ya kuundwa',
                    'created-by'   => 'Imeundwa na',
                    'last-updated' => 'Ilisasishwa mwisho',
                ],
            ],

            'statistics' => [
                'title' => 'Takwimu',

                'entries' => [
                    'sub-tasks'         => 'Kazi ndogo',
                    'timesheet-entries' => 'Maingizo ya rejista ya muda',
                ],
            ],
        ],
    ],
];
