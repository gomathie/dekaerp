<?php

return [
    'navigation' => [
        'title' => 'Kanuni za kuhifadhi',
        'group' => 'Usimamizi wa ghala',
    ],

    'form' => [
        'fields' => [
            'in-location'          => 'Bidhaa inapofika katika',
            'product'              => 'Bidhaa',
            'product-placeholder'  => 'Bidhaa zote',
            'category'             => 'Kategoria ya bidhaa',
            'category-placeholder' => 'Kategoria zote',
            'storage-category'     => 'Kategoria ya uhifadhi',
            'out-location'         => 'Hifadhi katika',
            'sub-location'         => 'Eneo dogo',
            'company'              => 'Kampuni',
        ],
    ],

    'table' => [
        'columns' => [
            'in-location'      => 'Bidhaa inapofika katika',
            'product'          => 'Bidhaa',
            'category'         => 'Kategoria ya bidhaa',
            'storage-category' => 'Kategoria ya uhifadhi',
            'out-location'     => 'Hifadhi katika',
            'sub-location'     => 'Eneo dogo',
            'company'          => 'Kampuni',
            'deleted-at'       => 'Ilifutwa',
            'created-at'       => 'Iliundwa',
            'updated-at'       => 'Ilisasishwa',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Kanuni ya kuhifadhi imesasishwa',
                    'body'  => 'Kanuni ya kuhifadhi imesasishwa kwa mafanikio.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Kanuni ya kuhifadhi imerejeshwa',
                    'body'  => 'Kanuni ya kuhifadhi imerejeshwa kwa mafanikio.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Kanuni ya kuhifadhi imefutwa',
                    'body'  => 'Kanuni ya kuhifadhi imefutwa kwa mafanikio.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'error' => [
                        'title' => 'Kanuni ya kuhifadhi haikuweza kufutwa',
                        'body'  => 'Kanuni ya kuhifadhi haiwezi kufutwa kabisa kwa sababu inatumiwa na rekodi nyingine.',
                    ],

                    'success' => [
                        'title' => 'Kanuni ya kuhifadhi imefutwa kabisa',
                        'body'  => 'Kanuni ya kuhifadhi imefutwa kabisa kwa mafanikio.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Kanuni za kuhifadhi zimerejeshwa',
                    'body'  => 'Kanuni za kuhifadhi zimerejeshwa kwa mafanikio.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Kanuni za kuhifadhi zimefutwa',
                    'body'  => 'Kanuni za kuhifadhi zimefutwa kwa mafanikio.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'error' => [
                        'title' => 'Kanuni za kuhifadhi hazikuweza kufutwa',
                        'body'  => 'Baadhi ya kanuni za kuhifadhi haziwezi kufutwa kabisa kwa sababu zinatumiwa na rekodi nyingine.',
                    ],

                    'success' => [
                        'title' => 'Kanuni za kuhifadhi zimefutwa kabisa',
                        'body'  => 'Kanuni za kuhifadhi zimefutwa kabisa kwa mafanikio.',
                    ],
                ],
            ],
        ],
    ],
];
