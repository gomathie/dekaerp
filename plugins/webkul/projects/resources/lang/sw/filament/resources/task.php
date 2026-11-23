<?php

return [
    'title' => 'Kazi',

    'navigation' => [
        'title' => 'Kazi',
    ],

    'global-search' => [
        'project'   => 'Mradi',
        'customer'  => 'Mteja',
        'milestone' => 'Hatua kuu',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Jumla',

                'fields' => [
                    'title'             => 'Kichwa',
                    'title-placeholder' => 'Kazi Kichwa...',
                    'tags'              => 'Lebo',
                    'name'              => 'Jina',
                    'color'             => 'Rangi',
                    'description'       => 'Maelezo',
                    'project'           => 'Mradi',
                    'status'            => 'Hali',
                    'start_date'        => 'Anza Tarehe',
                    'end_date'          => 'Mwisho Tarehe',
                ],
            ],

            'additional' => [
                'title' => 'Additional Taarifa',
            ],

            'settings' => [
                'title' => 'Mipangilio',

                'fields' => [
                    'project'                     => 'Mradi',
                    'milestone'                   => 'Hatua kuu',
                    'milestone-hint-text'         => 'Kiotomatiki deliver yako services upon reaching hatua kuu na linking hii hadi agizo la mauzo kipengee.',
                    'name'                        => 'Jina',
                    'deadline'                    => 'Deadline',
                    'is-completed'                => 'Ni Imekamilika',
                    'customer'                    => 'Mteja',
                    'assignees'                   => 'Assignees',
                    'allocated-hours'             => 'Allocated Saa',
                    'allocated-hours-helper-text' => 'Katika saa (Eg. 1.5 saa means 1 saa 30 dakika)',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'id'                  => 'Kitambulisho',
            'priority'            => 'Kipaumbele',
            'state'               => 'Hali',
            'new-state'           => 'Mpya Hali',
            'update-state'        => 'Sasisha Hali',
            'title'               => 'Kichwa',
            'project'             => 'Mradi',
            'project-placeholder' => 'Binafsi Kazi',
            'milestone'           => 'Hatua kuu',
            'customer'            => 'Mteja',
            'assignees'           => 'Assignees',
            'allocated-time'      => 'Allocated Time',
            'time-spent'          => 'Time Spent',
            'time-remaining'      => 'Time Iliyobaki',
            'progress'            => 'Maendeleo',
            'deadline'            => 'Deadline',
            'tags'                => 'Lebo',
            'stage'               => 'Hatua',
        ],

        'groups' => [
            'state'      => 'Hali',
            'project'    => 'Mradi',
            'milestone'  => 'Hatua kuu',
            'customer'   => 'Mteja',
            'deadline'   => 'Deadline',
            'stage'      => 'Hatua',
            'created-at' => 'Imeundwa',
        ],

        'filters' => [
            'title'             => 'Kichwa',
            'priority'          => 'Kipaumbele',
            'low'               => 'Chini',
            'high'              => 'Juu',
            'state'             => 'Hali',
            'tags'              => 'Lebo',
            'allocated-hours'   => 'Allocated Saa',
            'total-hours-spent' => 'Jumla Saa Spent',
            'remaining-hours'   => 'Iliyobaki Saa',
            'overtime'          => 'Muda wa ziada',
            'progress'          => 'Maendeleo',
            'deadline'          => 'Deadline',
            'created-at'        => 'Imeundwa',
            'updated-at'        => 'Imesasishwa',
            'assignees'         => 'Assignees',
            'customer'          => 'Mteja',
            'project'           => 'Mradi',
            'stage'             => 'Hatua',
            'milestone'         => 'Hatua kuu',
            'company'           => 'Kampuni',
            'creator'           => 'Creator',
        ],

        'actions' => [
            'update-state' => [
                'modal-heading' => 'Sasisha Kazi Hali',
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Kazi restored',
                    'body'  => 'kazi ina restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Kazi imefutwa',
                    'body'  => 'kazi ina imefutwa successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Kazi force imefutwa',
                    'body'  => 'kazi ina force imefutwa successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Kazi restored',
                    'body'  => 'kazi ina restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Kazi imefutwa',
                    'body'  => 'kazi ina imefutwa successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Kazi force imefutwa',
                    'body'  => 'kazi ina force imefutwa successfully.',
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
                'title' => 'Mradi Taarifa',

                'entries' => [
                    'project'   => 'Mradi',
                    'milestone' => 'Hatua kuu',
                    'customer'  => 'Mteja',
                    'assignees' => 'Assignees',
                    'deadline'  => 'Deadline',
                    'stage'     => 'Hatua',
                ],
            ],

            'time-tracking' => [
                'title' => 'Time Tracking',

                'entries' => [
                    'allocated-time'        => 'Allocated Time',
                    'time-spent'            => 'Time Spent',
                    'time-spent-suffix'     => ' Saa',
                    'time-remaining'        => 'Time Iliyobaki',
                    'time-remaining-suffix' => ' Saa',
                    'progress'              => 'Maendeleo',
                ],
            ],

            'additional-information' => [
                'title' => 'Additional Taarifa',
            ],

            'record-information' => [
                'title' => 'Rekodi Taarifa',

                'entries' => [
                    'created-at'   => 'Imeundwa',
                    'created-by'   => 'Imeundwa Na',
                    'last-updated' => 'Mwisho Imesasishwa',
                ],
            ],

            'statistics' => [
                'title' => 'Statistics',

                'entries' => [
                    'sub-tasks'         => 'Sub Kazi',
                    'timesheet-entries' => 'Karatasi ya muda Maingizo',
                ],
            ],
        ],
    ],
];
