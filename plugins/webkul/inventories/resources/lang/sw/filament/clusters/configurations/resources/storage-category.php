<?php

return [
    'navigation' => [
        'title' => 'Storage Kategoria',
        'group' => 'Ghala Management',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Jumla',

                'fields' => [
                    'name'               => 'Jina',
                    'allow-new-products' => 'Ruhusu Mpya Bidhaa',
                    'max-weight'         => 'Max Uzito',
                    'company'            => 'Kampuni',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'               => 'Jina',
            'allow-new-products' => 'Ruhusu Mpya Bidhaa',
            'max-weight'         => 'Max Uzito',
            'company'            => 'Kampuni',
            'deleted-at'         => 'Imefutwa',
            'created-at'         => 'Imeundwa',
            'updated-at'         => 'Imesasishwa',
        ],

        'groups' => [
            'allow-new-products' => 'Ruhusu Mpya Bidhaa',
            'created-at'         => 'Imeundwa',
            'updated-at'         => 'Imesasishwa',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Storage Kategoria imefutwa',
                    'body'  => 'storage kategoria ina imefutwa successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Storage Kategoria imefutwa',
                    'body'  => 'storage kategoria ina imefutwa successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'  => 'Jumla',

                'entries' => [
                    'name'               => 'Jina',
                    'allow-new-products' => 'Ruhusu Mpya Bidhaa',
                    'max-weight'         => 'Max Uzito',
                    'company'            => 'Kampuni',
                ],
            ],

            'record-information' => [
                'title' => 'Rekodi Taarifa',

                'entries' => [
                    'created-by'   => 'Imeundwa Na',
                    'created-at'   => 'Imeundwa',
                    'last-updated' => 'Mwisho Imesasishwa',
                ],
            ],
        ],
    ],
];
