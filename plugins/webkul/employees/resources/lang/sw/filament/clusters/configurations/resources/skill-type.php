<?php

return [
    'title' => 'Skill Types',

    'navigation' => [
        'title' => 'Skill Types',
        'group' => 'Mfanyakazi',
    ],

    'form' => [
        'sections' => [
            'fields' => [
                'name'             => 'Jina',
                'name-placeholder' => 'Ingiza skill aina jina',
                'color'            => 'Rangi',
                'status'           => 'Hali',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'id'         => 'Kitambulisho',
            'name'       => 'Skill Aina',
            'status'     => 'Hali',
            'color'      => 'Rangi',
            'skills'     => 'Skills',
            'levels'     => 'Levels',
            'created-by' => 'Imeundwa Na',
            'created-at' => 'Imeundwa',
            'updated-at' => 'Imesasishwa',
        ],

        'filters' => [
            'skill-levels' => 'Skill Levels',
            'skills'       => 'Skills',
            'created-by'   => 'Imeundwa Na',
            'status'       => 'Hali',
            'updated-at'   => 'Imesasishwa',
            'created-at'   => 'Imeundwa',
        ],

        'groups' => [
            'name'       => 'Skill Aina',
            'color'      => 'Rangi',
            'status'     => 'Hali',
            'created-by' => 'Imeundwa Na',
            'created-at' => 'Imeundwa',
            'updated-at' => 'Imesasishwa',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Skill Aina restored',
                    'body'  => 'Skill Aina ina restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Skill Aina imefutwa',
                    'body'  => 'Skill Aina ina imefutwa successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Skill Types restored',
                    'body'  => 'Skill Types ina restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Skill Types imefutwa',
                    'body'  => 'Skill Types ina imefutwa successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Skill Types force imefutwa',
                    'body'  => 'Skill Types ina force imefutwa successfully.',
                ],
            ],
        ],

        'empty-state-actions' => [
            'create' => [
                'notification' => [
                    'title' => 'Skill Types',
                    'body'  => 'Skill Types ina imeundwa successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'entries' => [
                'name'   => 'Skill Aina',
                'color'  => 'Rangi',
                'status' => 'Hali',
            ],
        ],
    ],
];
