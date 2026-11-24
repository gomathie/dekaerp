<?php

return [
    'global-search' => [
        'reference' => 'Rejea',
        'barcode'   => 'Msimbopau',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Jumla',

                'fields' => [
                    'name'             => 'Jina',
                    'name-placeholder' => 'mf. Fulana',
                    'description'      => 'Maelezo',
                    'tags'             => 'Lebo',
                ],
            ],

            'images' => [
                'title' => 'Picha',
            ],

            'inventory' => [
                'title' => 'Bidhaa ghalani',

                'fields' => [],

                'fieldsets' => [
                    'logistics' => [
                        'title' => 'Usafirishaji',

                        'fields' => [
                            'weight' => 'Uzito',
                            'volume' => 'Ujazo',
                        ],
                    ],
                ],
            ],

            'settings' => [
                'title' => 'Mipangilio',

                'fields' => [
                    'type'                => 'Aina',
                    'reference'           => 'Rejea',
                    'barcode'             => 'Msimbopau',
                    'category'            => 'Kategoria',
                    'company'             => 'Kampuni',
                    'company-placeholder' => 'Kampuni zote',
                ],
            ],

            'pricing' => [
                'title' => 'Bei',

                'fields' => [
                    'price'           => 'Bei',
                    'cost'            => 'Gharama',
                    'uom-placeholder' => 'Kipimo',
                ],
            ],

            'additional' => [
                'title' => 'Ziada',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'favorite'            => 'Pendwa',
            'name'                => 'Jina',
            'variants'            => 'Aina za bidhaa',
            'images'              => 'Picha',
            'type'                => 'Aina',
            'reference'           => 'Rejea',
            'responsible'         => 'Msimamizi',
            'barcode'             => 'Msimbopau',
            'category'            => 'Kategoria',
            'company'             => 'Kampuni',
            'company-placeholder' => 'Kampuni zote',
            'price'               => 'Bei',
            'cost'                => 'Gharama',
            'on-hand'             => 'Zilizopo',
            'tags'                => 'Lebo',
            'deleted-at'          => 'Ilifutwa',
            'created-at'          => 'Iliundwa',
            'updated-at'          => 'Ilisasishwa',
        ],

        'groups' => [
            'type'       => 'Aina',
            'category'   => 'Kategoria',
            'created-at' => 'Iliundwa',
        ],

        'filters' => [
            'name'        => 'Jina',
            'type'        => 'Aina',
            'reference'   => 'Rejea',
            'barcode'     => 'Msimbopau',
            'category'    => 'Kategoria',
            'company'     => 'Kampuni',
            'price'       => 'Bei',
            'cost'        => 'Gharama',
            'is-favorite' => 'Ni pendwa',
            'weight'      => 'Uzito',
            'volume'      => 'Ujazo',
            'tags'        => 'Lebo',
            'responsible' => 'Msimamizi',
            'created-at'  => 'Iliundwa',
            'updated-at'  => 'Ilisasishwa',
            'creator'     => 'Muundaji',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Bidhaa imerejeshwa',
                    'body'  => 'Bidhaa imerejeshwa kikamilifu.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Bidhaa imefutwa',
                    'body'  => 'Bidhaa imefutwa kikamilifu.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Bidhaa imefutwa kabisa',
                        'body'  => 'Bidhaa imefutwa kabisa kikamilifu.',
                    ],

                    'error' => [
                        'title' => 'Bidhaa haikuweza kufutwa',
                        'body'  => 'Bidhaa haiwezi kufutwa kwa sababu inatumika kwa sasa.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'print' => [
                'label' => 'Chapisha lebo',

                'form' => [
                    'fields' => [
                        'quantity' => 'Idadi ya lebo',
                        'format'   => 'Muundo',

                        'format-options' => [
                            'dymo'       => 'Dymo',
                            '2x7_price'  => '2x7 pamoja na bei',
                            '4x7_price'  => '4x7 pamoja na bei',
                            '4x12'       => '4x12',
                            '4x12_price' => '4x12 pamoja na bei',
                        ],
                    ],
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Bidhaa zimerejeshwa',
                    'body'  => 'Bidhaa zimerejeshwa kikamilifu.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Bidhaa zimefutwa',
                    'body'  => 'Bidhaa zimefutwa kikamilifu.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Bidhaa zimefutwa kabisa',
                        'body'  => 'Bidhaa zimefutwa kabisa kikamilifu.',
                    ],

                    'error' => [
                        'title' => 'Bidhaa hazikuweza kufutwa',
                        'body'  => 'Bidhaa haziwezi kufutwa kwa sababu zinatumika kwa sasa.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Jumla',

                'entries' => [
                    'name'             => 'Jina',
                    'name-placeholder' => 'mf. Fulana',
                    'description'      => 'Maelezo',
                    'tags'             => 'Lebo',
                ],
            ],

            'images' => [
                'title' => 'Picha',

                'entries' => [],
            ],

            'settings' => [
                'title' => 'Mipangilio',

                'entries' => [
                    'type'      => 'Aina',
                    'reference' => 'Rejea',
                    'barcode'   => 'Msimbopau',
                    'category'  => 'Kategoria',
                    'company'   => 'Kampuni',
                ],
            ],

            'pricing' => [
                'title' => 'Bei',

                'entries' => [
                    'price' => 'Bei',
                    'cost'  => 'Gharama',
                ],
            ],

            'inventory' => [
                'title' => 'Bidhaa ghalani',

                'fieldsets' => [
                    'logistics' => [
                        'title' => 'Usafirishaji',

                        'entries' => [
                            'weight' => 'Uzito',
                            'volume' => 'Ujazo',
                        ],
                    ],
                ],
            ],

            'record-information' => [
                'title' => 'Taarifa za rekodi',

                'entries' => [
                    'created-at' => 'Iliundwa',
                    'created-by' => 'Imeundwa na',
                    'updated-at' => 'Ilisasishwa',
                ],
            ],
        ],
    ],
];
