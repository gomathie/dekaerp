<?php

return [
    'navigation' => [
        'title' => 'Kifurushi Types',
        'group' => 'Usafirishaji',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Jumla',

                'fields' => [
                    'name'       => 'Jina',
                    'barcode'    => 'Msimbo pau',
                    'company'    => 'Kampuni',
                    'weight'     => 'Uzito',
                    'max-weight' => 'Max Uzito',

                    'fieldsets' => [
                        'size' => [
                            'title' => 'Ukubwa',

                            'fields' => [
                                'length' => 'Length',
                                'width'  => 'Upana',
                                'height' => 'Urefu',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'       => 'Jina',
            'barcode'    => 'Msimbo pau',
            'weight'     => 'Uzito',
            'max-weight' => 'Max Uzito',
            'width'      => 'Upana',
            'height'     => 'Urefu',
            'length'     => 'Length',
            'created-at' => 'Imeundwa',
            'updated-at' => 'Imesasishwa',
        ],

        'groups' => [
            'created-at' => 'Imeundwa',
            'updated-at' => 'Imesasishwa',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Kifurushi Aina imefutwa',
                    'body'  => 'kifurushi aina ina imefutwa successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Kifurushi Aina imefutwa',
                    'body'  => 'kifurushi aina ina imefutwa successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'   => 'Jumla Taarifa',
                'entries' => [
                    'name'      => 'Jina',
                    'fieldsets' => [
                        'size' => [
                            'title'   => 'Kifurushi Dimensions',
                            'entries' => [
                                'length' => 'Length',
                                'width'  => 'Upana',
                                'height' => 'Urefu',
                            ],
                        ],
                    ],
                    'weight'     => 'Base Uzito',
                    'max-weight' => 'Maximum Uzito',
                    'barcode'    => 'Msimbo pau',
                    'company'    => 'Kampuni',
                    'created-at' => 'Imeundwa',
                    'updated-at' => 'Mwisho Imesasishwa',
                ],
            ],

            'record-information' => [
                'title' => 'Rekodi Taarifa',

                'entries' => [
                    'created-by'   => 'Imeundwa Na',
                    'created-at'   => 'Imeundwa',
                    'last-updated' => 'Mwisho Imesasishwa',
                ],
            ],
        ],
    ],
];
