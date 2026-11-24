<?php

return [
    'navigation' => [
        'title' => 'Mipango ya shughuli',
        'group' => 'Shughuli',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Taarifa za jumla',
                'fields' => [
                    'name'       => 'Jina',
                    'status'     => 'Hali',
                    'department' => 'Idara',
                    'company'    => 'Kampuni',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'       => 'Jina',
            'status'     => 'Hali',
            'department' => 'Idara',
            'company'    => 'Kampuni',
            'manager'    => 'Meneja',
            'created-by' => 'Iliundwa na',
            'created-at' => 'Iliundwa',
            'updated-at' => 'Ilisasishwa',
        ],

        'filters' => [
            'name'           => 'Jina',
            'plugin'         => 'Programu-jalizi',
            'activity-types' => 'Aina za shughuli',
            'company'        => 'Kampuni',
            'department'     => 'Idara',
            'is-active'      => 'Hali',
            'updated-at'     => 'Ilisasishwa',
            'created-at'     => 'Iliundwa',
        ],

        'groups' => [
            'status'     => 'Hali',
            'name'       => 'Jina',
            'created-by' => 'Iliundwa na',
            'created-at' => 'Iliundwa',
            'updated-at' => 'Ilisasishwa',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Mpango wa shughuli umerejeshwa',
                    'body'  => 'Mpango wa shughuli umerejeshwa kwa mafanikio.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Mpango wa shughuli umefutwa',
                    'body'  => 'Mpango wa shughuli umefutwa kwa mafanikio.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Mpango wa shughuli umefutwa kabisa',
                    'body'  => 'Mpango wa shughuli umefutwa kabisa kwa mafanikio.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Mipango ya shughuli imerejeshwa',
                    'body'  => 'Mipango ya shughuli imerejeshwa kwa mafanikio.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Mipango ya shughuli imefutwa',
                    'body'  => 'Mipango ya shughuli imefutwa kwa mafanikio.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Mipango ya shughuli imefutwa kabisa',
                    'body'  => 'Mipango ya shughuli imefutwa kabisa kwa mafanikio.',
                ],
            ],
        ],

        'empty-state' => [
            'create' => [
                'notification' => [
                    'title' => 'Mpango wa shughuli umeundwa',
                    'body'  => 'Mpango wa shughuli umeundwa kwa mafanikio.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'   => 'Taarifa za jumla',
                'entries' => [
                    'name'       => 'Jina',
                    'status'     => 'Hali',
                    'department' => 'Idara',
                    'manager'    => 'Meneja',
                    'company'    => 'Kampuni',
                ],
            ],
        ],
    ],
];
