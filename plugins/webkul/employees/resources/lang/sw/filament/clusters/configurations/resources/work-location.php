<?php

return [
    'title' => 'Work Maeneo',

    'navigation' => [
        'title' => 'Work Maeneo',
        'group' => 'Mfanyakazi',
    ],

    'form' => [
        'name'            => 'Jina',
        'company'         => 'Kampuni',
        'location-type'   => 'Mahali Aina',
        'location-number' => 'Mahali Nambari',
        'status'          => 'Hali',
    ],

    'table' => [
        'columns' => [
            'id'              => 'Kitambulisho',
            'name'            => 'Jina',
            'status'          => 'Hali',
            'company'         => 'Kampuni',
            'location-type'   => 'Mahali Aina',
            'location-number' => 'Mahali Nambari',
            'deleted-at'      => 'Imefutwa',
            'created-by'      => 'Imeundwa Na',
            'created-at'      => 'Imeundwa',
            'updated-at'      => 'Imesasishwa',
        ],

        'filters' => [
            'name'            => 'Jina',
            'status'          => 'Hali',
            'created-by'      => 'Imeundwa Na',
            'company'         => 'Kampuni',
            'location-number' => 'Mahali Nambari',
            'location-type'   => 'Mahali Aina',
            'updated-at'      => 'Imesasishwa',
            'created-at'      => 'Imeundwa',
        ],

        'groups' => [
            'name'          => 'Jina',
            'status'        => 'Hali',
            'location-type' => 'Mahali Aina',
            'company'       => 'Kampuni',
            'created-by'    => 'Imeundwa Na',
            'created-at'    => 'Imeundwa',
            'updated-at'    => 'Imesasishwa',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Work Mahali imesasishwa',
                    'body'  => 'work Mahali ina imesasishwa successfully.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Work Mahali restored',
                    'body'  => 'work Mahali ina restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Work Mahali imefutwa',
                    'body'  => 'work Mahali ina imefutwa successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Work Mahali force imefutwa',
                    'body'  => 'work Mahali ina force imefutwa successfully.',
                ],
            ],

            'empty-state' => [
                'notification' => [
                    'title' => 'Work Mahali imeundwa',
                    'body'  => 'Work Mahali ina imeundwa successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Work Maeneo imefutwa',
                    'body'  => 'work Maeneo ina imefutwa successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Work Maeneo force imefutwa',
                    'body'  => 'work Maeneo ina force imefutwa successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'name'            => 'Jina',
        'company'         => 'Kampuni',
        'location-type'   => 'Mahali Aina',
        'location-number' => 'Mahali Nambari',
        'status'          => 'Hali',
    ],
];
