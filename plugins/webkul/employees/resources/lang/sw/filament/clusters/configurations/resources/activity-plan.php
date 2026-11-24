<?php

return [
    'navigation' => [
        'title' => 'Mipango ya shughuli',
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
            'created-by' => 'Imeundwa na',
            'created-at' => 'Imeundwa',
            'updated-at' => 'Imesasishwa',
        ],

        'filters' => [
            'name'           => 'Jina',
            'plugin'         => 'Programu-jalizi',
            'activity-types' => 'Aina za shughuli',
            'company'        => 'Kampuni',
            'department'     => 'Idara',
            'is-active'      => 'Hali',
            'updated-at'     => 'Imesasishwa',
            'created-at'     => 'Imeundwa',
        ],

        'groups' => [
            'status'     => 'Hali',
            'name'       => 'Jina',
            'created-by' => 'Imeundwa na',
            'created-at' => 'Imeundwa',
            'updated-at' => 'Imesasishwa',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Mpango wa shughuli umerejeshwa',
                    'body'  => 'Mpango wa shughuli umerejeshwa kikamilifu.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Mpango wa shughuli umefutwa',
                    'body'  => 'Mpango wa shughuli umefutwa kikamilifu.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Mpango wa shughuli umefutwa kabisa',
                    'body'  => 'Mpango wa shughuli umefutwa kabisa.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Mipango ya shughuli imerejeshwa',
                    'body'  => 'Mipango ya shughuli imerejeshwa kikamilifu.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Mipango ya shughuli imefutwa',
                    'body'  => 'Mipango ya shughuli imefutwa kikamilifu.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Mipango ya shughuli imefutwa kabisa',
                    'body'  => 'Mipango ya shughuli imefutwa kabisa.',
                ],
            ],
        ],

        'activity-plan' => [
            'create' => [
                'notification' => [
                    'title' => 'Mpango wa shughuli umeundwa',
                    'body'  => 'Mpango wa shughuli umeundwa kikamilifu.',
                ],
            ],
        ],

        'empty-state' => [
            'create' => [
                'notification' => [
                    'title' => 'Mpango wa shughuli umeundwa',
                    'body'  => 'Mpango wa shughuli umeundwa kikamilifu.',
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
