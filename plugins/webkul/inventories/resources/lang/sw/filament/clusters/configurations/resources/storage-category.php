<?php

return [
    'navigation' => [
        'title' => 'Kategoria za uhifadhi',
        'group' => 'Usimamizi wa ghala',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Jumla',

                'fields' => [
                    'name'               => 'Jina',
                    'allow-new-products' => 'Ruhusu bidhaa mpya',
                    'max-weight'         => 'Uzito wa juu',
                    'company'            => 'Kampuni',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'               => 'Jina',
            'allow-new-products' => 'Ruhusu bidhaa mpya',
            'max-weight'         => 'Uzito wa juu',
            'company'            => 'Kampuni',
            'deleted-at'         => 'Ilifutwa',
            'created-at'         => 'Iliundwa',
            'updated-at'         => 'Ilisasishwa',
        ],

        'groups' => [
            'allow-new-products' => 'Ruhusu bidhaa mpya',
            'created-at'         => 'Iliundwa',
            'updated-at'         => 'Ilisasishwa',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Kategoria ya uhifadhi imefutwa',
                    'body'  => 'Kategoria ya uhifadhi imefutwa kwa mafanikio.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Kategoria za uhifadhi zimefutwa',
                    'body'  => 'Kategoria za uhifadhi zimefutwa kwa mafanikio.',
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
                    'allow-new-products' => 'Ruhusu bidhaa mpya',
                    'max-weight'         => 'Uzito wa juu',
                    'company'            => 'Kampuni',
                ],
            ],

            'record-information' => [
                'title' => 'Taarifa za rekodi',

                'entries' => [
                    'created-by'   => 'Imeundwa na',
                    'created-at'   => 'Iliundwa',
                    'last-updated' => 'Ilisasishwa mwisho',
                ],
            ],
        ],
    ],
];
