<?php

return [
    'title' => 'Siku za lazima',

    'model-label' => 'Siku ya lazima',

    'navigation' => [
        'title' => 'Siku za lazima',
    ],

    'form' => [
        'fields' => [
            'name'       => 'Jina',
            'start-date' => 'Tarehe ya kuanza',
            'end-date'   => 'Tarehe ya kumalizika',
            'color'      => 'Rangi',
        ],
    ],

    'table' => [
        'columns' => [
            'name'         => 'Jina',
            'company-name' => 'Kampuni',
            'created-by'   => 'Imeundwa na',
            'start-date'   => 'Tarehe ya kuanza',
            'end-date'     => 'Tarehe ya kumalizika',
        ],

        'filters' => [
            'name'         => 'Jina',
            'company-name' => 'Kampuni',
            'created-by'   => 'Imeundwa na',
            'start-date'   => 'Tarehe ya kuanza',
            'end-date'     => 'Tarehe ya kumalizika',
            'created-at'   => 'Iliundwa',
            'updated-at'   => 'Ilisasishwa',
        ],

        'groups' => [
            'name'         => 'Jina',
            'company-name' => 'Kampuni',
            'created-by'   => 'Imeundwa na',
            'start-date'   => 'Tarehe ya kuanza',
            'end-date'     => 'Tarehe ya kumalizika',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Siku ya lazima imesasishwa',
                    'body'  => 'Siku ya lazima imesasishwa kwa mafanikio.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Siku ya lazima imefutwa',
                    'body'  => 'Siku ya lazima imefutwa kwa mafanikio.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Siku za lazima zimefutwa',
                    'body'  => 'Siku za lazima zimefutwa kwa mafanikio.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'name'       => 'Jina',
            'start-date' => 'Tarehe ya kuanza',
            'end-date'   => 'Tarehe ya kumalizika',
            'color'      => 'Rangi',
        ],
    ],
];
