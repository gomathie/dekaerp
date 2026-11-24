<?php

return [
    'navigation' => [
        'title' => 'Bidhaa',
        'group' => 'Hisa',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Jumla',

                'fields' => [
                    'name'             => 'Jina',
                    'name-placeholder' => 'mf. fulana',
                    'description'      => 'Maelezo',
                    'tags'             => 'Lebo',
                    'sales'            => 'Mauzo',
                    'purchase'         => 'Ununuzi',
                ],
            ],

            'invoice-policy' => [
                'title'            => 'Sera ya ankara',
                'ordered-policy'   => 'Unaweza kutoza ankara ya bidhaa kabla hazijawasilishwa.',
                'delivered-policy' => 'Toza ankara baada ya uwasilishaji, kulingana na idadi iliyowasilishwa, si iliyoagizwa.',
            ],

            'images' => [
                'title' => 'Picha',
            ],

            'settings' => [
                'title' => 'Mipangilio',

                'fields' => [
                    'type'      => 'Aina',
                    'reference' => 'Kumbukumbu',
                    'barcode'   => 'Msimbopau',
                    'category'  => 'Kategoria',
                    'company'   => 'Kampuni',
                ],
            ],

            'category-and-tags' => [
                'title' => 'Kategoria na lebo',

                'fields' => [
                    'category' => 'Kategoria',
                    'tags'     => 'Lebo',
                ],
            ],

            'pricing' => [
                'title' => 'Bei',

                'fields' => [
                    'price' => 'Bei',
                    'cost'  => 'Gharama',
                ],
            ],

            'additional' => [
                'title' => 'Ziada',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'        => 'Jina',
            'images'      => 'Picha',
            'type'        => 'Aina',
            'reference'   => 'Kumbukumbu',
            'responsible' => 'Msimamizi',
            'barcode'     => 'Msimbopau',
            'category'    => 'Kategoria',
            'company'     => 'Kampuni',
            'price'       => 'Bei',
            'cost'        => 'Gharama',
            'tags'        => 'Lebo',
            'deleted-at'  => 'Ilifutwa',
            'created-at'  => 'Iliundwa',
            'updated-at'  => 'Ilisasishwa',
        ],

        'groups' => [
            'type'       => 'Aina',
            'category'   => 'Kategoria',
            'created-at' => 'Iliundwa',
        ],

        'filters' => [
            'name'        => 'Jina',
            'type'        => 'Aina',
            'reference'   => 'Kumbukumbu',
            'barcode'     => 'Msimbopau',
            'category'    => 'Kategoria',
            'company'     => 'Kampuni',
            'price'       => 'Bei',
            'cost'        => 'Gharama',
            'is-favorite' => 'Ni kipendwa',
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
                    'body'  => 'Bidhaa imerejeshwa.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Bidhaa imefutwa',
                    'body'  => 'Bidhaa imefutwa.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Bidhaa imefutwa kabisa',
                    'body'  => 'Bidhaa imefutwa kabisa.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Bidhaa zimerejeshwa',
                    'body'  => 'Bidhaa zimerejeshwa.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Bidhaa zimefutwa',
                    'body'  => 'Bidhaa zimefutwa.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Bidhaa zimefutwa kabisa',
                    'body'  => 'Bidhaa zimefutwa kabisa.',
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
                    'name-placeholder' => 'mf. fulana',
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
                    'reference' => 'Kumbukumbu',
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
                'title' => 'Hisa',

                'entries' => [],

                'fieldsets' => [
                    'tracking' => [
                        'title' => 'Ufuatiliaji',

                        'entries' => [
                            'track-inventory' => 'Fuatilia hisa',
                            'track-by'        => 'Fuatilia kwa',
                            'expiration-date' => 'Tarehe ya kuisha muda',
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
                            'responsible' => 'Msimamizi',
                            'weight'      => 'Uzito',
                            'volume'      => 'Ujazo',
                            'sale-delay'  => 'Muda wa kumfikishia mteja (siku)',
                        ],
                    ],

                    'traceability' => [
                        'title' => 'Ufuatiliaji',

                        'entries' => [
                            'expiration-date'  => 'Kuisha muda (siku)',
                            'best-before-date' => 'Bora kabla ya (siku)',
                            'removal-date'     => 'Tarehe ya kuondoa (siku)',
                            'alert-date'       => 'Tarehe ya tahadhari (siku)',
                        ],
                    ],
                ],
            ],

            'record-information' => [
                'title' => 'Taarifa za rekodi',

                'entries' => [
                    'created-at' => 'Iliundwa',
                    'created-by' => 'Iliundwa na',
                    'updated-at' => 'Ilisasishwa',
                ],
            ],
        ],
    ],
];
