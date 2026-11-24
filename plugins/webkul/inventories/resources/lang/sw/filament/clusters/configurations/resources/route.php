<?php

return [
    'navigation' => [
        'title' => 'Njia',
        'group' => 'Usimamizi wa ghala',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Jumla',

                'fields' => [
                    'route'             => 'Njia',
                    'route-placeholder' => 'mf. Upokeaji wa hatua mbili',
                    'company'           => 'Kampuni',
                ],
            ],

            'applicable-on' => [
                'title'       => 'Inatumika kwa',
                'description' => 'Chagua maeneo ambapo njia hii inaweza kutumika.',

                'fields' => [
                    'products'                        => 'Bidhaa',
                    'products-hint-tooltip'           => 'Ikichaguliwa, njia hii itapatikana kuchaguliwa kwenye bidhaa.',
                    'product-categories'              => 'Kategoria za bidhaa',
                    'product-categories-hint-tooltip' => 'Ikichaguliwa, njia hii itapatikana kuchaguliwa kwenye kategoria ya bidhaa.',
                    'warehouses'                      => 'Maghala',
                    'warehouses-hint-tooltip'         => 'Ghala likiwekwa kwenye njia hii, njia hii itakuwa chaguomsingi kwa bidhaa zinazopita katika ghala hilo.',
                    'packaging'                       => 'Ufungashaji',
                    'packaging-hint-tooltip'          => 'Ikichaguliwa, njia hii itapatikana kuchaguliwa kwenye ufungashaji.',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'route'      => 'Njia',
            'company'    => 'Kampuni',
            'deleted-at' => 'Ilifutwa',
            'created-at' => 'Iliundwa',
            'updated-at' => 'Ilisasishwa',
        ],

        'groups' => [
            'created-at' => 'Iliundwa',
            'updated-at' => 'Ilisasishwa',
        ],

        'filters' => [
            'company'  => 'Kampuni',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Njia imesasishwa',
                    'body'  => 'Njia imesasishwa kwa mafanikio.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Njia imerejeshwa',
                    'body'  => 'Njia imerejeshwa kwa mafanikio.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Njia imefutwa',
                    'body'  => 'Njia imefutwa kwa mafanikio.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Njia imefutwa kabisa',
                        'body'  => 'Njia imefutwa kabisa kwa mafanikio.',
                    ],

                    'error' => [
                        'title' => 'Njia haikuweza kufutwa',
                        'body'  => 'Njia haiwezi kufutwa kwa sababu inatumika.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Njia zimerejeshwa',
                    'body'  => 'Njia zimerejeshwa kwa mafanikio.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Njia zimefutwa',
                    'body'  => 'Njia zimefutwa kwa mafanikio.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Njia zimefutwa kabisa',
                        'body'  => 'Njia zimefutwa kabisa kwa mafanikio.',
                    ],

                    'error' => [
                        'title' => 'Njia hazikuweza kufutwa',
                        'body'  => 'Njia haziwezi kufutwa kwa sababu zinatumika.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'  => 'Jumla',

                'entries' => [
                    'route'             => 'Njia',
                    'route-placeholder' => 'mf. Upokeaji wa hatua mbili',
                    'company'           => 'Kampuni',
                ],
            ],

            'applicable-on' => [
                'title'       => 'Inatumika kwa',
                'description' => 'Chagua mahali ambapo njia hii inaweza kuchaguliwa.',

                'entries' => [
                    'products'                        => 'Bidhaa',
                    'products-hint-tooltip'           => 'Ikichaguliwa, njia hii itapatikana kuchaguliwa kwenye bidhaa.',
                    'product-categories'              => 'Kategoria za bidhaa',
                    'product-categories-hint-tooltip' => 'Ikichaguliwa, njia hii itapatikana kuchaguliwa kwenye kategoria ya bidhaa.',
                    'warehouses'                      => 'Maghala',
                    'warehouses-hint-tooltip'         => 'Ghala likiwekwa kwenye njia hii, njia hii itakuwa chaguomsingi kwa bidhaa zinazopita katika ghala hilo.',
                    'packaging'                       => 'Ufungashaji',
                    'packaging-hint-tooltip'          => 'Ikichaguliwa, njia hii itapatikana kuchaguliwa kwenye ufungashaji.',
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
