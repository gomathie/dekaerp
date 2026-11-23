<?php

return [
    'form' => [
        'name'          => 'Jina',
        'level'         => 'Level',
        'default-level' => 'Chaguo-msingi Level',
    ],

    'table' => [
        'columns' => [
            'name'          => 'Jina',
            'level'         => 'Level',
            'default-level' => 'Chaguo-msingi Level',
            'created-at'    => 'Imeundwa',
            'updated-at'    => 'Imesasishwa',
        ],

        'groups' => [
            'created-at' => 'Imeundwa',
        ],

        'filters' => [
            'deleted-records' => 'Imefutwa Rekodi',
        ],

        'actions' => [
            'create' => [
                'notification' => [
                    'title' => 'Skill Level imeundwa',
                    'body'  => 'skill level ina imeundwa successfully.',
                ],
            ],

            'edit' => [
                'notification' => [
                    'title' => 'Skill Level imesasishwa',
                    'body'  => 'skill level ina imesasishwa successfully.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Skill Level restored',
                    'body'  => 'skill level ina restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Skill Level imefutwa',
                    'body'  => 'skill level ina imefutwa successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Skill Levels imefutwa',
                    'body'  => 'skills ina imefutwa successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Skill Levels force imefutwa',
                    'body'  => 'skills ina force imefutwa successfully.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Skill Levels force restored',
                    'body'  => 'skills ina force restored successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'name'          => 'Jina',
            'level'         => 'Level',
            'default-level' => 'Chaguo-msingi Level',
        ],
    ],
];
