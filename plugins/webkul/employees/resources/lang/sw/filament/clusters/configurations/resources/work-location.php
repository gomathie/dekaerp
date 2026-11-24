<?php

return [
    'title' => 'Maeneo ya kazi',

    'navigation' => [
        'title' => 'Maeneo ya kazi',
        'group' => 'Mfanyakazi',
    ],

    'form' => [
        'name'            => 'Jina',
        'company'         => 'Kampuni',
        'location-type'   => 'Aina ya eneo',
        'location-number' => 'Nambari ya eneo',
        'status'          => 'Hali',
    ],

    'table' => [
        'columns' => [
            'id'              => 'ID',
            'name'            => 'Jina',
            'status'          => 'Hali',
            'company'         => 'Kampuni',
            'location-type'   => 'Aina ya eneo',
            'location-number' => 'Nambari ya eneo',
            'deleted-at'      => 'Imefutwa',
            'created-by'      => 'Imeundwa na',
            'created-at'      => 'Imeundwa',
            'updated-at'      => 'Imesasishwa',
        ],

        'filters' => [
            'name'            => 'Jina',
            'status'          => 'Hali',
            'created-by'      => 'Imeundwa na',
            'company'         => 'Kampuni',
            'location-number' => 'Nambari ya eneo',
            'location-type'   => 'Aina ya eneo',
            'updated-at'      => 'Imesasishwa',
            'created-at'      => 'Imeundwa',
        ],

        'groups' => [
            'name'          => 'Jina',
            'status'        => 'Hali',
            'location-type' => 'Aina ya eneo',
            'company'       => 'Kampuni',
            'created-by'    => 'Imeundwa na',
            'created-at'    => 'Imeundwa',
            'updated-at'    => 'Imesasishwa',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Eneo la kazi limesasishwa',
                    'body'  => 'Eneo la kazi limesasishwa kikamilifu.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Eneo la kazi limerejeshwa',
                    'body'  => 'Eneo la kazi limerejeshwa kikamilifu.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Eneo la kazi limefutwa',
                    'body'  => 'Eneo la kazi limefutwa kikamilifu.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Eneo la kazi limefutwa kabisa',
                    'body'  => 'Eneo la kazi limefutwa kabisa.',
                ],
            ],

            'empty-state' => [
                'notification' => [
                    'title' => 'Eneo la kazi limeundwa',
                    'body'  => 'Eneo la kazi limeundwa kikamilifu.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Maeneo ya kazi yamefutwa',
                    'body'  => 'Maeneo ya kazi yamefutwa kikamilifu.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Maeneo ya kazi yamefutwa kabisa',
                    'body'  => 'Maeneo ya kazi yamefutwa kabisa.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'name'            => 'Jina',
        'company'         => 'Kampuni',
        'location-type'   => 'Aina ya eneo',
        'location-number' => 'Nambari ya eneo',
        'status'          => 'Hali',
    ],
];
