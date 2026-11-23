<?php

return [
    'form' => [
        'sections' => [
            'fields' => [
                'title'        => 'Kichwa',
                'type'         => 'Aina',
                'name'         => 'Jina',
                'type'         => 'Aina',
                'create-type'  => 'Unda Aina',
                'duration'     => 'Duration',
                'start-date'   => 'Anza Tarehe',
                'end-date'     => 'Mwisho Tarehe',
                'display-type' => 'Display Aina',
                'description'  => 'Maelezo',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'title'        => 'Kichwa',
            'start-date'   => 'Anza Tarehe',
            'end-date'     => 'Mwisho Tarehe',
            'display-type' => 'Display Aina',
            'description'  => 'Maelezo',
            'created-by'   => 'Imeundwa Na',
            'created-at'   => 'Imeundwa',
            'updated-at'   => 'Imesasishwa',
        ],

        'groups' => [
            'group-by-type'         => 'Kundi Na Aina',
            'group-by-display-type' => 'Kundi Na Display Aina',
        ],

        'header-actions' => [
            'add-resume' => 'Ongeza Resume',
        ],

        'filters' => [
            'type'            => 'Aina',
            'start-date-from' => 'Anza Tarehe Kutoka',
            'start-date-to'   => 'Anza Tarehe Hadi',
            'created-from'    => 'Imeundwa Kutoka',
            'created-to'      => 'Imeundwa Hadi',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Skill Level imesasishwa',
                    'body'  => 'skill level ina imesasishwa successfully.',
                ],
            ],

            'create' => [
                'notification' => [
                    'title' => 'Skill Level imeundwa',
                    'body'  => 'skill level ina imeundwa successfully.',
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
                    'title' => 'Skills imefutwa',
                    'body'  => 'skills ina imefutwa successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'title'        => 'Kichwa',
            'display-type' => 'Display Aina',
            'type'         => 'Aina',
            'description'  => 'Maelezo',
            'duration'     => 'Duration',
            'start-date'   => 'Anza Tarehe',
            'end-date'     => 'Mwisho Tarehe',
        ],
    ],
];
