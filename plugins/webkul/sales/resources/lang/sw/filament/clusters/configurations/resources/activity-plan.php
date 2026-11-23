<?php

return [
    'navigation' => [
        'title' => 'Shughuli Plans',
        'group' => 'Shughuli',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Jumla Taarifa',
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
            'created-by' => 'Imeundwa Na',
            'created-at' => 'Imeundwa',
            'updated-at' => 'Imesasishwa',
        ],

        'filters' => [
            'name'           => 'Jina',
            'plugin'         => 'Programu-jalizi',
            'activity-types' => 'Shughuli Types',
            'company'        => 'Kampuni',
            'department'     => 'Idara',
            'is-active'      => 'Hali',
            'updated-at'     => 'Imesasishwa',
            'created-at'     => 'Imeundwa',
        ],

        'groups' => [
            'status'     => 'Hali',
            'name'       => 'Jina',
            'created-by' => 'Imeundwa Na',
            'created-at' => 'Imeundwa',
            'updated-at' => 'Imesasishwa',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Shughuli Plan restored',
                    'body'  => 'shughuli plan ina restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Shughuli Plan imefutwa',
                    'body'  => 'shughuli plan ina imefutwa successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Shughuli Plan force imefutwa',
                    'body'  => 'shughuli plan ina force imefutwa successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Shughuli Plans restored',
                    'body'  => 'shughuli plans ina restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Shughuli Plans imefutwa',
                    'body'  => 'shughuli plans ina imefutwa successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Shughuli Plans force imefutwa',
                    'body'  => 'shughuli plans ina force imefutwa successfully.',
                ],
            ],
        ],

        'empty-state' => [
            'create' => [
                'notification' => [
                    'title' => 'Shughuli Plan imeundwa',
                    'body'  => 'shughuli plan ina imeundwa successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'   => 'Jumla Taarifa',
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
