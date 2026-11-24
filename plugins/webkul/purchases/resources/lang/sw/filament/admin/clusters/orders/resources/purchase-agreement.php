<?php

return [
    'navigation' => [
        'title' => 'Makubaliano ya ununuzi',
        'group' => 'Ununuzi',
    ],

    'global-search' => [
        'vendor' => 'Msambazaji',
        'type'   => 'Aina',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Jumla',

                'fields' => [
                    'vendor'                => 'Msambazaji',
                    'valid-from'            => 'Halali kuanzia',
                    'valid-to'              => 'Halali hadi',
                    'buyer'                 => 'Mnunuzi',
                    'reference'             => 'Kumbukumbu',
                    'reference-placeholder' => 'mf. PO/123',
                    'agreement-type'        => 'Aina ya makubaliano',
                    'company'               => 'Kampuni',
                    'currency'              => 'Sarafu',
                ],
            ],
        ],

        'tabs' => [
            'products' => [
                'title' => 'Bidhaa',

                'columns' => [
                    'product'    => 'Bidhaa',
                    'quantity'   => 'Idadi',
                    'ordered'    => 'Imeagizwa',
                    'uom'        => 'Kipimo',
                    'unit-price' => 'Bei ya kipimo',
                ],

                'fields' => [
                    'product'    => 'Bidhaa',
                    'quantity'   => 'Idadi',
                    'ordered'    => 'Imeagizwa',
                    'uom'        => 'Kipimo',
                    'unit-price' => 'Bei ya kipimo',
                ],
            ],

            'additional' => [
                'title' => 'Taarifa za ziada',
            ],

            'terms' => [
                'title' => 'Sheria na masharti',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'agreement'      => 'Makubaliano',
            'vendor'         => 'Msambazaji',
            'agreement-type' => 'Aina ya makubaliano',
            'buyer'          => 'Mnunuzi',
            'company'        => 'Kampuni',
            'valid-from'     => 'Halali kuanzia',
            'valid-to'       => 'Halali hadi',
            'reference'      => 'Kumbukumbu',
            'status'         => 'Hali',
        ],

        'groups' => [
            'agreement-type' => 'Aina ya makubaliano',
            'vendor'         => 'Msambazaji',
            'state'          => 'Hali',
            'created-at'     => 'Iliundwa',
            'updated-at'     => 'Ilisasishwa',
        ],

        'filters' => [
            'agreement'      => 'Makubaliano',
            'vendor'         => 'Msambazaji',
            'agreement-type' => 'Aina ya makubaliano',
            'buyer'          => 'Mnunuzi',
            'company'        => 'Kampuni',
            'valid-from'     => 'Halali kuanzia',
            'valid-to'       => 'Halali hadi',
            'reference'      => 'Kumbukumbu',
            'status'         => 'Hali',
            'created-at'     => 'Iliundwa',
            'updated-at'     => 'Ilisasishwa',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Makubaliano ya ununuzi yamefutwa',
                    'body'  => 'Makubaliano ya ununuzi yamefutwa.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Makubaliano ya ununuzi yamerejeshwa',
                    'body'  => 'Makubaliano ya ununuzi yamerejeshwa.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Makubaliano ya ununuzi yamefutwa kabisa',
                        'body'  => 'Makubaliano ya ununuzi yamefutwa kabisa.',
                    ],

                    'error' => [
                        'title' => 'Makubaliano ya ununuzi hayakuweza kufutwa',
                        'body'  => 'Makubaliano ya ununuzi hayawezi kufutwa kwa sababu yanatumika.',
                    ],

                    'warning' => [
                        'title' => 'Makubaliano ya ununuzi hayawezi kufutwa',
                        'body'  => 'Makubaliano ya ununuzi yaliyo katika hali ya Rasimu au Imeghairiwa pekee ndiyo yanaweza kufutwa.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Makubaliano ya ununuzi yamefutwa',
                    'body'  => 'Makubaliano ya ununuzi yamefutwa.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Makubaliano ya ununuzi yamerejeshwa',
                    'body'  => 'Makubaliano ya ununuzi yamerejeshwa.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Makubaliano ya ununuzi yamefutwa kabisa',
                        'body'  => 'Makubaliano ya ununuzi yamefutwa kabisa.',
                    ],

                    'error' => [
                        'title' => 'Makubaliano ya ununuzi hayakuweza kufutwa',
                        'body'  => 'Makubaliano ya ununuzi hayawezi kufutwa kwa sababu yanatumika.',
                    ],

                    'warning' => [
                        'title' => 'Makubaliano ya ununuzi hayawezi kufutwa',
                        'body'  => 'Makubaliano ya ununuzi yaliyo katika hali ya Rasimu au Imeghairiwa pekee ndiyo yanaweza kufutwa.',
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
                    'vendor'                => 'Msambazaji',
                    'valid-from'            => 'Halali kuanzia',
                    'valid-to'              => 'Halali hadi',
                    'buyer'                 => 'Mnunuzi',
                    'reference'             => 'Kumbukumbu',
                    'reference-placeholder' => 'mf. PO/123',
                    'agreement-type'        => 'Aina ya makubaliano',
                    'company'               => 'Kampuni',
                    'currency'              => 'Sarafu',
                ],
            ],

            'metadata' => [
                'title' => 'Metadata',

                'entries' => [
                    'created-at' => 'Iliundwa',
                    'created-by' => 'Iliundwa na',
                    'updated-at' => 'Ilisasishwa',
                ],
            ],
        ],

        'tabs' => [
            'products' => [
                'title' => 'Bidhaa',

                'entries' => [
                    'product'    => 'Bidhaa',
                    'quantity'   => 'Idadi',
                    'ordered'    => 'Imeagizwa',
                    'uom'        => 'Kipimo',
                    'unit-price' => 'Bei ya kipimo',
                ],
            ],

            'additional' => [
                'title' => 'Taarifa za ziada',
            ],

            'terms' => [
                'title' => 'Sheria na masharti',
            ],
        ],
    ],
];
