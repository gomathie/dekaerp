<?php

return [
    'title' => 'Ujuzi',

    'navigation' => [
        'title' => 'Ujuzi',
    ],

    'form' => [
        'sections' => [
            'skill-details' => [
                'title' => 'Maelezo ya ujuzi',

                'fields' => [
                    'employee'       => 'Mfanyakazi',
                    'skill'          => 'Ujuzi',
                    'skill-level'    => 'Kiwango',
                    'skill-type'     => 'Aina ya ujuzi',
                ],
            ],
            'addition-information' => [
                'title' => 'Taarifa za ziada',

                'fields' => [
                    'created-by' => 'Imeundwa na',
                    'updated-by' => 'Imesasishwa na',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'id'              => 'ID',
            'employee'        => 'Mfanyakazi',
            'skill'           => 'Ujuzi',
            'skill-level'     => 'Kiwango',
            'skill-type'      => 'Aina ya ujuzi',
            'user'            => 'Mtumiaji',
            'proficiency'     => 'Umahiri',
            'created-by'      => 'Imeundwa na',
            'created-at'      => 'Imeundwa',
        ],

        'filters' => [
            'employee'        => 'Mfanyakazi',
            'skill'           => 'Ujuzi',
            'skill-level'     => 'Kiwango',
            'skill-type'      => 'Aina ya ujuzi',
            'user'            => 'Mtumiaji',
            'created-by'      => 'Imeundwa na',
            'created-at'      => 'Imeundwa',
            'updated-at'      => 'Imesasishwa',
        ],

        'groups' => [
            'employee'   => 'Mfanyakazi',
            'skill-type' => 'Aina ya ujuzi',
        ],
    ],

    'infolist' => [
        'sections' => [
            'skill-details' => [
                'title' => 'Maelezo ya ujuzi',

                'entries' => [
                    'employee'        => 'Mfanyakazi',
                    'skill'           => 'Ujuzi',
                    'skill-level'     => 'Kiwango',
                    'skill-type'      => 'Aina ya ujuzi',
                ],
            ],

            'additional-information' => [
                'title' => 'Taarifa za ziada',

                'entries' => [
                    'created-by' => 'Imeundwa na',
                    'updated-by' => 'Imesasishwa na',
                ],
            ],
        ],
    ],
];
