<?php

return [
    'title' => 'Skills',

    'navigation' => [
        'title' => 'Skills',
    ],

    'form' => [
        'sections' => [
            'skill-details' => [
                'title' => 'Skill Maelezo',

                'fields' => [
                    'employee'       => 'Mfanyakazi',
                    'skill'          => 'Skill',
                    'skill-level'    => 'Level',
                    'skill-type'     => 'Skill Aina',
                ],
            ],
            'addition-information' => [
                'title' => 'Additional Taarifa',

                'fields' => [
                    'created-by' => 'Imeundwa Na',
                    'updated-by' => 'Imesasishwa Na',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'id'              => 'Kitambulisho',
            'employee'        => 'Mfanyakazi',
            'skill'           => 'Skill',
            'skill-level'     => 'Level',
            'skill-type'      => 'Skill Aina',
            'user'            => 'Mtumiaji',
            'proficiency'     => 'Proficiency',
            'created-by'      => 'Imeundwa Na',
            'created-at'      => 'Imeundwa',
        ],

        'filters' => [
            'employee'        => 'Mfanyakazi',
            'skill'           => 'Skill',
            'skill-level'     => 'Level',
            'skill-type'      => 'Skill Aina',
            'user'            => 'Mtumiaji',
            'created-by'      => 'Imeundwa Na',
            'created-at'      => 'Imeundwa',
            'updated-at'      => 'Imesasishwa',
        ],

        'groups' => [
            'employee'   => 'Mfanyakazi',
            'skill-type' => 'Skill Aina',
        ],
    ],

    'infolist' => [
        'sections' => [
            'skill-details' => [
                'title' => 'Skill Maelezo',

                'entries' => [
                    'employee'        => 'Mfanyakazi',
                    'skill'           => 'Skill',
                    'skill-level'     => 'Level',
                    'skill-type'      => 'Skill Aina',
                ],
            ],

            'additional-information' => [
                'title' => 'Additional Taarifa',

                'entries' => [
                    'created-by' => 'Imeundwa Na',
                    'updated-by' => 'Imesasishwa Na',
                ],
            ],
        ],
    ],
];
