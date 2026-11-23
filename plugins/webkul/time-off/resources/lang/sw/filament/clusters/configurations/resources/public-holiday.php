<?php

return [
    'title' => 'Hadharani Holidays',

    'model-label' => 'Hadharani holiday',

    'navigation' => [
        'title' => 'Hadharani Holidays',
    ],

    'form' => [
        'fields' => [
            'name'             => 'Jina',
            'name-placeholder' => 'Ingiza jina ya hadharani holiday',
            'date-from'        => 'Anza Tarehe',
            'date-to'          => 'Mwisho Tarehe',
            'color'            => 'Rangi',
            'calendar'         => 'Kalenda',
        ],
    ],

    'table' => [
        'columns' => [
            'name'         => 'Jina',
            'company-name' => 'Kampuni Jina',
            'calendar'     => 'Kalenda',
            'created-by'   => 'Imeundwa Na',
            'date-from'    => 'Anza Tarehe',
            'date-to'      => 'Mwisho Tarehe',
        ],

        'filters' => [
            'name'         => 'Jina',
            'company-name' => 'Kampuni Jina',
            'created-by'   => 'Imeundwa Na',
            'date-from'    => 'Anza Tarehe',
            'date-to'      => 'Mwisho Tarehe',
            'created-at'   => 'Imeundwa',
            'updated-at'   => 'Imesasishwa',
        ],

        'groups' => [
            'name'         => 'Jina',
            'company-name' => 'Kampuni Jina',
            'created-by'   => 'Imeundwa Na',
            'date-from'    => 'Anza Tarehe',
            'date-to'      => 'Mwisho Tarehe',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Hadharani holiday imesasishwa',
                    'body'  => 'hadharani holiday ina restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Hadharani holiday imefutwa',
                    'body'  => 'hadharani holiday ina imefutwa successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Hadharani holidays imefutwa',
                    'body'  => 'hadharani holidays ina imefutwa successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'name'      => 'Jina',
            'date-from' => 'Anza Tarehe',
            'date-to'   => 'Mwisho Tarehe',
            'color'     => 'Rangi',
        ],
    ],
];
