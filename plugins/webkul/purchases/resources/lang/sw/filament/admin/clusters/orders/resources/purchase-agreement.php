<?php

return [
    'navigation' => [
        'title' => 'Ununuzi Agreements',
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
                    'valid-from'            => 'Halali Kutoka',
                    'valid-to'              => 'Halali Mpaka',
                    'buyer'                 => 'Buyer',
                    'reference'             => 'Kumbukumbu',
                    'reference-placeholder' => 'eg. PO/123',
                    'agreement-type'        => 'Agreement Aina',
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
                    'quantity'   => 'Kiasi',
                    'ordered'    => 'Ordered',
                    'uom'        => 'Kipimo',
                    'unit-price' => 'Bei ya uniti',
                ],

                'fields' => [
                    'product'    => 'Bidhaa',
                    'quantity'   => 'Kiasi',
                    'ordered'    => 'Ordered',
                    'uom'        => 'Kipimo',
                    'unit-price' => 'Bei ya uniti',
                ],
            ],

            'additional' => [
                'title' => 'Additional Taarifa',
            ],

            'terms' => [
                'title' => 'Terms na Conditions',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'agreement'      => 'Agreement',
            'vendor'         => 'Msambazaji',
            'agreement-type' => 'Agreement Aina',
            'buyer'          => 'Buyer',
            'company'        => 'Kampuni',
            'valid-from'     => 'Halali Kutoka',
            'valid-to'       => 'Halali Mpaka',
            'reference'      => 'Kumbukumbu',
            'status'         => 'Hali',
        ],

        'groups' => [
            'agreement-type' => 'Agreement Aina',
            'vendor'         => 'Msambazaji',
            'state'          => 'Hali',
            'created-at'     => 'Imeundwa',
            'updated-at'     => 'Imesasishwa',
        ],

        'filters' => [
            'agreement'      => 'Agreement',
            'vendor'         => 'Msambazaji',
            'agreement-type' => 'Agreement Aina',
            'buyer'          => 'Buyer',
            'company'        => 'Kampuni',
            'valid-from'     => 'Halali Kutoka',
            'valid-to'       => 'Halali Mpaka',
            'reference'      => 'Kumbukumbu',
            'status'         => 'Hali',
            'created-at'     => 'Imeundwa',
            'updated-at'     => 'Imesasishwa',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Ununuzi Agreement imefutwa',
                    'body'  => 'ununuzi agreement ina imefutwa successfully.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Ununuzi Agreement restored',
                    'body'  => 'ununuzi agreement ina restored successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Ununuzi Agreement permanently imefutwa',
                        'body'  => 'ununuzi agreement ina permanently imefutwa successfully.',
                    ],

                    'error' => [
                        'title' => 'Ununuzi Agreement could si kuwa imefutwa',
                        'body'  => 'ununuzi agreement haiwezekani kuwa imefutwa kwa sababu hii ni currently katika tumia.',
                    ],

                    'warning' => [
                        'title' => 'Ununuzi Agreement haiwezekani kuwa imefutwa',
                        'body'  => 'Tu ununuzi agreements katika Rasimu au Imeghairiwa hali unaweza kuwa imefutwa.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Ununuzi Agreements imefutwa',
                    'body'  => 'ununuzi agreements ina imefutwa successfully.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Ununuzi Agreements restored',
                    'body'  => 'ununuzi agreements ina restored successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Ununuzi Agreements permanently imefutwa',
                        'body'  => 'ununuzi agreements ina permanently imefutwa successfully.',
                    ],

                    'error' => [
                        'title' => 'Ununuzi Agreements could si kuwa imefutwa',
                        'body'  => 'ununuzi agreements haiwezekani kuwa imefutwa kwa sababu wao ni currently katika tumia.',
                    ],

                    'warning' => [
                        'title' => 'Ununuzi Agreement haiwezekani kuwa imefutwa',
                        'body'  => 'Tu ununuzi agreements katika Rasimu au Imeghairiwa hali unaweza kuwa imefutwa.',
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
                    'valid-from'            => 'Halali Kutoka',
                    'valid-to'              => 'Halali Mpaka',
                    'buyer'                 => 'Buyer',
                    'reference'             => 'Kumbukumbu',
                    'reference-placeholder' => 'eg. PO/123',
                    'agreement-type'        => 'Agreement Aina',
                    'company'               => 'Kampuni',
                    'currency'              => 'Sarafu',
                ],
            ],

            'metadata' => [
                'title' => 'Metadata',

                'entries' => [
                    'created-at' => 'Imeundwa',
                    'created-by' => 'Imeundwa Na',
                    'updated-at' => 'Imesasishwa',
                ],
            ],
        ],

        'tabs' => [
            'products' => [
                'title' => 'Bidhaa',

                'entries' => [
                    'product'    => 'Bidhaa',
                    'quantity'   => 'Kiasi',
                    'ordered'    => 'Ordered',
                    'uom'        => 'Kipimo',
                    'unit-price' => 'Bei ya uniti',
                ],
            ],

            'additional' => [
                'title' => 'Additional Taarifa',
            ],

            'terms' => [
                'title' => 'Terms na Conditions',
            ],
        ],
    ],
];
