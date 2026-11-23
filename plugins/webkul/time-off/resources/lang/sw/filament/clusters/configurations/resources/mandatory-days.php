<?php

return [
    'title' => 'Mandatory Siku',

    'model-label' => 'Mandatory Siku',

    'navigation' => [
        'title' => 'Mandatory Holidays',
    ],

    'form' => [
        'fields' => [
            'name'       => 'Jina',
            'start-date' => 'Anza Tarehe',
            'end-date'   => 'Mwisho Tarehe',
            'color'      => 'Rangi',
        ],
    ],

    'table' => [
        'columns' => [
            'name'         => 'Jina',
            'company-name' => 'Kampuni Jina',
            'created-by'   => 'Imeundwa Na',
            'start-date'   => 'Anza Tarehe',
            'end-date'     => 'Mwisho Tarehe',
        ],

        'filters' => [
            'name'         => 'Jina',
            'company-name' => 'Kampuni Jina',
            'created-by'   => 'Imeundwa Na',
            'start-date'   => 'Anza Tarehe',
            'end-date'     => 'Mwisho Tarehe',
            'created-at'   => 'Imeundwa',
            'updated-at'   => 'Imesasishwa',
        ],

        'groups' => [
            'name'         => 'Jina',
            'company-name' => 'Kampuni Jina',
            'created-by'   => 'Imeundwa Na',
            'start-date'   => 'Anza Tarehe',
            'end-date'     => 'Mwisho Tarehe',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Mandatory siku imesasishwa',
                    'body'  => 'mandatory siku ina restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Mandatory siku imefutwa',
                    'body'  => 'mandatory siku ina imefutwa successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Mandatory siku imefutwa',
                    'body'  => 'mandatory siku ina imefutwa successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'name'       => 'Jina',
            'start-date' => 'Anza Tarehe',
            'end-date'   => 'Mwisho Tarehe',
            'color'      => 'Rangi',
        ],
    ],
];
