<?php

return [
    'form' => [
        'sections' => [
            'fields' => [
                'skill-type'  => 'Aina ya ujuzi',
                'skill'       => 'Ujuzi',
                'skill-level' => 'Kiwango cha ujuzi',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'skill-type'    => 'Aina ya ujuzi',
            'skill'         => 'Ujuzi',
            'skill-level'   => 'Kiwango cha ujuzi',
            'level-percent' => 'Kiwango (%)',
            'created-by'    => 'Imeundwa na',
            'user'          => 'Mtumiaji',
            'created-at'    => 'Tarehe ya kuundwa',
        ],

        'groups' => [
            'skill-type' => 'Aina ya ujuzi',
        ],

        'header-actions' => [
            'add-skill' => 'Ongeza ujuzi',
        ],

        'filters' => [
            'activity-type'   => 'Aina ya shughuli',
            'activity-status' => 'Hali ya shughuli',
            'has-delay'       => 'Ina ucheleweshaji',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Ujuzi umesasishwa',
                    'body'  => 'Ujuzi umesasishwa kwa mafanikio.',
                ],
            ],

            'create' => [
                'notification' => [
                    'title' => 'Ujuzi umeundwa',
                    'body'  => 'Ujuzi umeundwa kwa mafanikio.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Ujuzi umefutwa',
                    'body'  => 'Ujuzi umefutwa kwa mafanikio.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Ujuzi umefutwa',
                    'body'  => 'Ujuzi umefutwa kwa mafanikio.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'skill-type'    => 'Aina ya ujuzi',
            'skill'         => 'Ujuzi',
            'skill-level'   => 'Kiwango cha ujuzi',
            'level-percent' => 'Kiwango (%)',
        ],
    ],
];
