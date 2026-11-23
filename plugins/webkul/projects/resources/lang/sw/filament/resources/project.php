<?php

return [
    'navigation' => [
        'title' => 'Miradi',
    ],

    'global-search' => [
        'project-manager' => 'Mradi Meneja',
        'customer'        => 'Mteja',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Jumla',

                'fields' => [
                    'name'             => 'Jina',
                    'name-placeholder' => 'Mradi Jina...',
                    'description'      => 'Maelezo',
                ],
            ],

            'additional' => [
                'title' => 'Additional Taarifa',

                'fields' => [
                    'project-manager'             => 'Mradi Meneja',
                    'customer'                    => 'Mteja',
                    'start-date'                  => 'Anza Tarehe',
                    'end-date'                    => 'Mwisho Tarehe',
                    'allocated-hours'             => 'Allocated Saa',
                    'allocated-hours-helper-text' => 'Katika saa (Eg. 1.5 saa means 1 saa 30 dakika)',
                    'tags'                        => 'Lebo',
                    'company'                     => 'Kampuni',
                ],
            ],

            'settings' => [
                'title' => 'Mipangilio',

                'fields' => [
                    'visibility'                   => 'Visibility',
                    'visibility-hint-tooltip'      => 'Ruhusu wafanyakazi hadi access yako mradi au kazi na adding wao kama followers. Wao ita kiotomatiki gain access hadi yoyote kazi amekabidhiwa hadi wao..',
                    'private-description'          => 'Invited ndani watumiaji tu.',
                    'internal-description'         => 'Zote ndani watumiaji unaweza see.',
                    'public-description'           => 'Invited lango watumiaji na zote ndani watumiaji.',
                    'time-management'              => 'Time Management',
                    'allow-timesheets'             => 'Ruhusu Karatasi za muda',
                    'allow-timesheets-helper-text' => 'Log time washa kazi na track maendeleo',
                    'task-management'              => 'Kazi Management',
                    'allow-milestones'             => 'Ruhusu Milestones',
                    'allow-milestones-helper-text' => 'Monitor key milestones hiyo ni essential kwa achieving imefanikiwa.',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'            => 'Jina',
            'customer'        => 'Mteja',
            'start-date'      => 'Anza Tarehe',
            'end-date'        => 'Mwisho Tarehe',
            'planned-date'    => 'Imepangwa Tarehe',
            'remaining-hours' => 'Iliyobaki Saa',
            'project-manager' => 'Mradi Meneja',
        ],

        'groups' => [
            'stage'           => 'Hatua',
            'project-manager' => 'Mradi Meneja',
            'customer'        => 'Mteja',
            'created-at'      => 'Imeundwa',
        ],

        'filters' => [
            'name'             => 'Jina',
            'visibility'       => 'Visibility',
            'start-date'       => 'Anza Tarehe',
            'end-date'         => 'Mwisho Tarehe',
            'allow-timesheets' => 'Ruhusu Karatasi za muda',
            'allow-milestones' => 'Ruhusu Milestones',
            'allocated-hours'  => 'Allocated Saa',
            'created-at'       => 'Imeundwa',
            'updated-at'       => 'Imesasishwa',
            'stage'            => 'Hatua',
            'customer'         => 'Mteja',
            'project-manager'  => 'Mradi Meneja',
            'company'          => 'Kampuni',
            'creator'          => 'Creator',
            'tags'             => 'Lebo',
        ],

        'actions' => [
            'tasks'      => ':count Kazi',
            'milestones' => ':completed milestones imekamilika nje ya :all',

            'restore' => [
                'notification' => [
                    'title' => 'Mradi restored',
                    'body'  => 'mradi ina restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Mradi imefutwa',
                    'body'  => 'mradi ina imefutwa successfully.',
                ],
            ],

            'force-delete' => [

                'notification' => [

                    'success' => [
                        'title' => 'Mradi permanently imefutwa',
                        'body'  => 'mradi ina permanently imefutwa successfully.',
                    ],

                    'error' => [
                        'title' => 'Mradi haiwezekani kuwa permanently imefutwa',
                        'body'  => 'mradi ni associated na nyingine rekodi.',
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
                    'name-placeholder' => 'Mradi Jina...',
                    'description'      => 'Maelezo',
                ],
            ],

            'additional' => [
                'title' => 'Additional Taarifa',

                'entries' => [
                    'project-manager'        => 'Mradi Meneja',
                    'customer'               => 'Mteja',
                    'project-timeline'       => 'Mradi Timeline',
                    'allocated-hours'        => 'Allocated Saa',
                    'allocated-hours-suffix' => ' Saa',
                    'remaining-hours'        => 'Iliyobaki Saa',
                    'remaining-hours-suffix' => ' Saa',
                    'current-stage'          => 'Sasa Hatua',
                    'tags'                   => 'Lebo',
                ],
            ],

            'statistics' => [
                'title' => 'Statistics',

                'entries' => [
                    'total-tasks'         => 'Jumla Kazi',
                    'milestones-progress' => 'Milestones Maendeleo',
                ],
            ],

            'record-information' => [
                'title' => 'Rekodi Taarifa',

                'entries' => [
                    'created-at'   => 'Imeundwa',
                    'created-by'   => 'Imeundwa Na',
                    'last-updated' => 'Mwisho Imesasishwa',
                ],
            ],

            'settings' => [
                'title' => 'Mradi Mipangilio',

                'entries' => [
                    'visibility'         => 'Visibility',
                    'timesheets-enabled' => 'Karatasi za muda Imewashwa',
                    'milestones-enabled' => 'Milestones Imewashwa',
                ],
            ],
        ],
    ],
];
