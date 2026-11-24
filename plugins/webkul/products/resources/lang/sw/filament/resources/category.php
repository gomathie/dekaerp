<?php

return [
    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Jumla',

                'fields' => [
                    'name'             => 'Jina',
                    'name-placeholder' => 'mf. Taa',
                    'parent'           => 'Kategoria kuu',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'        => 'Jina',
            'full-name'   => 'Jina kamili',
            'parent-path' => 'Njia ya kategoria kuu',
            'parent'      => 'Kategoria kuu',
            'creator'     => 'Muundaji',
            'created-at'  => 'Iliundwa',
            'created-at'  => 'Iliundwa',
            'updated-at'  => 'Ilisasishwa',
        ],

        'groups' => [
            'parent'     => 'Kategoria kuu',
            'creator'    => 'Muundaji',
            'created-at' => 'Iliundwa',
            'updated-at' => 'Ilisasishwa',
        ],

        'filters' => [
            'parent'  => 'Kategoria kuu',
            'creator' => 'Muundaji',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Kategoria imefutwa',
                        'body'  => 'Kategoria imefutwa kikamilifu.',
                    ],

                    'error' => [
                        'title' => 'Kategoria haikuweza kufutwa',
                        'body'  => 'Kategoria haiwezi kufutwa kwa sababu inatumika kwa sasa.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Kategoria zimefutwa',
                        'body'  => 'Kategoria zimefutwa kikamilifu.',
                    ],

                    'error' => [
                        'title' => 'Kategoria hazikuweza kufutwa',
                        'body'  => 'Kategoria haziwezi kufutwa kwa sababu zinatumika kwa sasa.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Taarifa za jumla',

                'entries' => [
                    'name'        => 'Jina',
                    'parent'      => 'Kategoria kuu',
                    'full_name'   => 'Jina kamili la kategoria',
                    'parent_path' => 'Njia ya kategoria',
                ],
            ],

            'record-information' => [
                'title' => 'Taarifa za rekodi',

                'entries' => [
                    'creator'    => 'Imeundwa na',
                    'created_at' => 'Iliundwa',
                    'updated_at' => 'Ilisasishwa mwisho',
                ],
            ],
        ],
    ],
];
