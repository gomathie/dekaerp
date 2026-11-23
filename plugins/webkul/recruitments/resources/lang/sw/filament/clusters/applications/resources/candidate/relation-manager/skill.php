<?php

return [
    'form' => [
        'sections' => [
            'fields' => [
                'skill-type'  => 'Skill Aina',
                'skill'       => 'Skill',
                'skill-level' => 'Skill Level',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'skill-type'    => 'Skill Aina',
            'skill'         => 'Skill',
            'skill-level'   => 'Skill Level',
            'level-percent' => 'Level Asilimia',
            'created-by'    => 'Imeundwa Na',
            'user'          => 'Mtumiaji',
            'created-at'    => 'Imeundwa',
        ],

        'groups' => [
            'skill-type' => 'Skill Aina',
        ],

        'header-actions' => [
            'add-skill' => 'Ongeza Skill',
        ],

        'filters' => [
            'activity-type'   => 'Shughuli Aina',
            'activity-status' => 'Shughuli Hali',
            'has-delay'       => 'Ina Delay',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Skill imesasishwa',
                    'body'  => 'skill ina imesasishwa successfully.',
                ],
            ],

            'create' => [
                'notification' => [
                    'title' => 'Skill imeundwa',
                    'body'  => 'skill ina imeundwa successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Skill imefutwa',
                    'body'  => 'skill ina imefutwa successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Skills imefutwa',
                    'body'  => 'skills ina imefutwa successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'skill-type'    => 'Skill Aina',
            'skill'         => 'Skill',
            'skill-level'   => 'Skill Level',
            'level-percent' => 'Level Asilimia',
        ],
    ],
];
