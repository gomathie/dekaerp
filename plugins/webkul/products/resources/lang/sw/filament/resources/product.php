<?php

return [
    'global-search' => [
        'reference' => 'Kumbukumbu',
        'barcode'   => 'Msimbo pau',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Jumla',

                'fields' => [
                    'name'             => 'Jina',
                    'name-placeholder' => 'eg. T-shirt',
                    'description'      => 'Maelezo',
                    'tags'             => 'Lebo',
                ],
            ],

            'images' => [
                'title' => 'Images',
            ],

            'inventory' => [
                'title' => 'Ghala',

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
                    'reference'           => 'Kumbukumbu',
                    'barcode'             => 'Msimbo pau',
                    'category'            => 'Kategoria',
                    'company'             => 'Kampuni',
                    'company-placeholder' => 'Zote Makampuni',
                ],
            ],

            'pricing' => [
                'title' => 'Pricing',

                'fields' => [
                    'price'           => 'Bei',
                    'cost'            => 'Gharama',
                    'uom-placeholder' => 'UOM',
                ],
            ],

            'additional' => [
                'title' => 'Additional',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'favorite'            => 'Favorite',
            'name'                => 'Jina',
            'variants'            => 'Variants',
            'images'              => 'Images',
            'type'                => 'Aina',
            'reference'           => 'Kumbukumbu',
            'responsible'         => 'Responsible',
            'barcode'             => 'Msimbo pau',
            'category'            => 'Kategoria',
            'company'             => 'Kampuni',
            'company-placeholder' => 'Zote Makampuni',
            'price'               => 'Bei',
            'cost'                => 'Gharama',
            'on-hand'             => 'Washa Mkono',
            'tags'                => 'Lebo',
            'deleted-at'          => 'Imefutwa',
            'created-at'          => 'Imeundwa',
            'updated-at'          => 'Imesasishwa',
        ],

        'groups' => [
            'type'       => 'Aina',
            'category'   => 'Kategoria',
            'created-at' => 'Imeundwa',
        ],

        'filters' => [
            'name'        => 'Jina',
            'type'        => 'Aina',
            'reference'   => 'Kumbukumbu',
            'barcode'     => 'Msimbo pau',
            'category'    => 'Kategoria',
            'company'     => 'Kampuni',
            'price'       => 'Bei',
            'cost'        => 'Gharama',
            'is-favorite' => 'Ni Favorite',
            'weight'      => 'Uzito',
            'volume'      => 'Ujazo',
            'tags'        => 'Lebo',
            'responsible' => 'Responsible',
            'created-at'  => 'Imeundwa',
            'updated-at'  => 'Imesasishwa',
            'creator'     => 'Creator',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Bidhaa restored',
                    'body'  => 'bidhaa ina restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Bidhaa imefutwa',
                    'body'  => 'bidhaa ina imefutwa successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Bidhaa force imefutwa',
                        'body'  => 'bidhaa ina force imefutwa successfully.',
                    ],

                    'error' => [
                        'title' => 'Bidhaa could si kuwa imefutwa',
                        'body'  => 'bidhaa haiwezekani kuwa imefutwa kwa sababu hii ni currently katika tumia.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'print' => [
                'label' => 'Chapisha Labels',

                'form' => [
                    'fields' => [
                        'quantity' => 'Nambari ya Labels',
                        'format'   => 'Umbizo',

                        'format-options' => [
                            'dymo'       => 'Dymo',
                            '2x7_price'  => '2x7 na bei',
                            '4x7_price'  => '4x7 na bei',
                            '4x12'       => '4x12',
                            '4x12_price' => '4x12 na bei',
                        ],
                    ],
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Bidhaa restored',
                    'body'  => 'bidhaa ina restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Bidhaa imefutwa',
                    'body'  => 'bidhaa ina imefutwa successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Bidhaa force imefutwa',
                        'body'  => 'bidhaa ina force imefutwa successfully.',
                    ],

                    'error' => [
                        'title' => 'Bidhaa could si kuwa imefutwa',
                        'body'  => 'bidhaa haiwezekani kuwa imefutwa kwa sababu wao ni currently katika tumia.',
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
                    'name-placeholder' => 'eg. T-shirt',
                    'description'      => 'Maelezo',
                    'tags'             => 'Lebo',
                ],
            ],

            'images' => [
                'title' => 'Images',

                'entries' => [],
            ],

            'settings' => [
                'title' => 'Mipangilio',

                'entries' => [
                    'type'      => 'Aina',
                    'reference' => 'Kumbukumbu',
                    'barcode'   => 'Msimbo pau',
                    'category'  => 'Kategoria',
                    'company'   => 'Kampuni',
                ],
            ],

            'pricing' => [
                'title' => 'Pricing',

                'entries' => [
                    'price' => 'Bei',
                    'cost'  => 'Gharama',
                ],
            ],

            'inventory' => [
                'title' => 'Ghala',

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
                'title' => 'Rekodi Taarifa',

                'entries' => [
                    'created-at' => 'Imeundwa',
                    'created-by' => 'Imeundwa Na',
                    'updated-at' => 'Imesasishwa',
                ],
            ],
        ],
    ],
];
