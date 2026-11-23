<?php

return [
    'navigation' => [
        'title' => 'Bidhaa',
        'group' => 'Ghala',
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
                    'sales'            => 'Mauzo',
                    'purchase'         => 'Ununuzi',
                ],
            ],

            'invoice-policy' => [
                'title'            => 'Ankara Policy',
                'ordered-policy'   => 'Wewe unaweza ankara goods kabla ya wao ni delivered.',
                'delivered-policy' => 'Ankara baada ya usafirishaji, based washa quantities delivered, si ordered.',
            ],

            'images' => [
                'title' => 'Images',
            ],

            'settings' => [
                'title' => 'Mipangilio',

                'fields' => [
                    'type'      => 'Aina',
                    'reference' => 'Kumbukumbu',
                    'barcode'   => 'Msimbo pau',
                    'category'  => 'Kategoria',
                    'company'   => 'Kampuni',
                ],
            ],

            'category-and-tags' => [
                'title' => 'Kategoria & Lebo',

                'fields' => [
                    'category' => 'Kategoria',
                    'tags'     => 'Lebo',
                ],
            ],

            'pricing' => [
                'title' => 'Pricing',

                'fields' => [
                    'price' => 'Bei',
                    'cost'  => 'Gharama',
                ],
            ],

            'additional' => [
                'title' => 'Additional',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'        => 'Jina',
            'images'      => 'Images',
            'type'        => 'Aina',
            'reference'   => 'Kumbukumbu',
            'responsible' => 'Responsible',
            'barcode'     => 'Msimbo pau',
            'category'    => 'Kategoria',
            'company'     => 'Kampuni',
            'price'       => 'Bei',
            'cost'        => 'Gharama',
            'tags'        => 'Lebo',
            'deleted-at'  => 'Imefutwa',
            'created-at'  => 'Imeundwa',
            'updated-at'  => 'Imesasishwa',
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
                    'title' => 'Bidhaa force imefutwa',
                    'body'  => 'bidhaa ina force imefutwa successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
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
                    'title' => 'Bidhaa force imefutwa',
                    'body'  => 'bidhaa ina force imefutwa successfully.',
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

                'entries' => [],

                'fieldsets' => [
                    'tracking' => [
                        'title' => 'Tracking',

                        'entries' => [
                            'track-inventory' => 'Track Ghala',
                            'track-by'        => 'Track Na',
                            'expiration-date' => 'Expiration Tarehe',
                        ],
                    ],

                    'operation' => [
                        'title' => 'Shughuli',

                        'entries' => [
                            'routes' => 'Njia',
                        ],
                    ],

                    'logistics' => [
                        'title' => 'Usafirishaji',

                        'entries' => [
                            'responsible' => 'Responsible',
                            'weight'      => 'Uzito',
                            'volume'      => 'Ujazo',
                            'sale-delay'  => 'Mteja Lead Time (Siku)',
                        ],
                    ],

                    'traceability' => [
                        'title' => 'Traceability',

                        'entries' => [
                            'expiration-date'  => 'Expiration Tarehe (Siku)',
                            'best-before-date' => 'Best Kabla ya Tarehe (Siku)',
                            'removal-date'     => 'Removal Tarehe (Siku)',
                            'alert-date'       => 'Alert Tarehe (Siku)',
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
