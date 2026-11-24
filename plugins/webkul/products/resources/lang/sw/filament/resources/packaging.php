<?php

return [
    'form' => [
        'name'    => 'Jina',
        'barcode' => 'Msimbopau',
        'product' => 'Bidhaa',
        'routes'  => 'Njia',
        'qty'     => 'Idadi',
        'company' => 'Kampuni',
    ],

    'table' => [
        'columns' => [
            'name'       => 'Jina',
            'product'    => 'Bidhaa',
            'routes'     => 'Njia',
            'qty'        => 'Idadi',
            'company'    => 'Kampuni',
            'barcode'    => 'Msimbopau',
            'created-at' => 'Iliundwa',
            'updated-at' => 'Ilisasishwa',
        ],

        'groups' => [
            'product'    => 'Bidhaa',
            'created-at' => 'Iliundwa',
            'updated-at' => 'Ilisasishwa',
        ],

        'filters' => [
            'product' => 'Bidhaa',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Kifungashio kimesasishwa',
                    'body'  => 'Kifungashio kimesasishwa kikamilifu.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Kifungashio kimefutwa',
                        'body'  => 'Kifungashio kimefutwa kikamilifu.',
                    ],

                    'error' => [
                        'title' => 'Kifungashio hakikuweza kufutwa',
                        'body'  => 'Kifungashio hakiwezi kufutwa kwa sababu kinatumika kwa sasa.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'print' => [
                'label' => 'Chapisha',
            ],

            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Vifungashio vimefutwa',
                        'body'  => 'Vifungashio vimefutwa kikamilifu.',
                    ],

                    'error' => [
                        'title' => 'Vifungashio havikuweza kufutwa',
                        'body'  => 'Vifungashio haviwezi kufutwa kwa sababu vinatumika kwa sasa.',
                    ],
                ],
            ],
        ],

        'empty-state-actions' => [
            'create' => [
                'label' => 'Kifungashio kipya',

                'notification' => [
                    'title' => 'Kifungashio kimeundwa',
                    'body'  => 'Kifungashio kimeundwa kikamilifu.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Taarifa za jumla',

                'entries' => [
                    'name'    => 'Jina la kifungashio',
                    'barcode' => 'Msimbopau',
                    'product' => 'Bidhaa',
                    'qty'     => 'Idadi',
                ],
            ],

            'organization' => [
                'title' => 'Taarifa za shirika',

                'entries' => [
                    'company'    => 'Kampuni',
                    'creator'    => 'Imeundwa na',
                    'created_at' => 'Iliundwa',
                    'updated_at' => 'Ilisasishwa mwisho',
                ],
            ],
        ],
    ],
];
