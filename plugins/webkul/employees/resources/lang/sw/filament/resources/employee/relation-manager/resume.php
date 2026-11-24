<?php

return [
    'form' => [
        'sections' => [
            'fields' => [
                'title'        => 'Kichwa',
                'type'         => 'Aina',
                'name'         => 'Jina',
                'type'         => 'Aina',
                'create-type'  => 'Unda aina',
                'duration'     => 'Muda',
                'start-date'   => 'Tarehe ya kuanza',
                'end-date'     => 'Tarehe ya kumaliza',
                'display-type' => 'Aina ya maonyesho',
                'description'  => 'Maelezo',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'title'        => 'Kichwa',
            'start-date'   => 'Tarehe ya kuanza',
            'end-date'     => 'Tarehe ya kumaliza',
            'display-type' => 'Aina ya maonyesho',
            'description'  => 'Maelezo',
            'created-by'   => 'Imeundwa na',
            'created-at'   => 'Imeundwa',
            'updated-at'   => 'Imesasishwa',
        ],

        'groups' => [
            'group-by-type'         => 'Panga kwa aina',
            'group-by-display-type' => 'Panga kwa aina ya maonyesho',
        ],

        'header-actions' => [
            'add-resume' => 'Ongeza wasifu',
        ],

        'filters' => [
            'type'            => 'Aina',
            'start-date-from' => 'Tarehe ya kuanza kuanzia',
            'start-date-to'   => 'Tarehe ya kuanza hadi',
            'created-from'    => 'Imeundwa kuanzia',
            'created-to'      => 'Imeundwa hadi',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Kipengele cha wasifu kimesasishwa',
                    'body'  => 'Kipengele cha wasifu kimesasishwa kwa mafanikio.',
                ],
            ],

            'create' => [
                'notification' => [
                    'title' => 'Kipengele cha wasifu kimeundwa',
                    'body'  => 'Kipengele cha wasifu kimeundwa kwa mafanikio.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Kipengele cha wasifu kimefutwa',
                    'body'  => 'Kipengele cha wasifu kimefutwa kwa mafanikio.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Vipengele vya wasifu vimefutwa',
                    'body'  => 'Vipengele vya wasifu vimefutwa kwa mafanikio.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'title'        => 'Kichwa',
            'display-type' => 'Aina ya maonyesho',
            'type'         => 'Aina',
            'description'  => 'Maelezo',
            'duration'     => 'Muda',
            'start-date'   => 'Tarehe ya kuanza',
            'end-date'     => 'Tarehe ya kumaliza',
        ],
    ],
];
