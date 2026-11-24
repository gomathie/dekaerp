<?php

return [
    'navigation' => [
        'title' => 'Aina za vifurushi',
        'group' => 'Uwasilishaji',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Jumla',

                'fields' => [
                    'name'       => 'Jina',
                    'barcode'    => 'Msimbopau',
                    'company'    => 'Kampuni',
                    'weight'     => 'Uzito',
                    'max-weight' => 'Uzito wa juu',

                    'fieldsets' => [
                        'size' => [
                            'title' => 'Ukubwa',

                            'fields' => [
                                'length' => 'Urefu',
                                'width'  => 'Upana',
                                'height' => 'Kimo',
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
            'barcode'    => 'Msimbopau',
            'weight'     => 'Uzito',
            'max-weight' => 'Uzito wa juu',
            'width'      => 'Upana',
            'height'     => 'Kimo',
            'length'     => 'Urefu',
            'created-at' => 'Iliundwa',
            'updated-at' => 'Ilisasishwa',
        ],

        'groups' => [
            'created-at' => 'Iliundwa',
            'updated-at' => 'Ilisasishwa',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Aina ya kifurushi imefutwa',
                    'body'  => 'Aina ya kifurushi imefutwa kwa mafanikio.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Aina ya kifurushi imefutwa',
                    'body'  => 'Aina ya kifurushi imefutwa kwa mafanikio.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'   => 'Taarifa za jumla',
                'entries' => [
                    'name'      => 'Jina',
                    'fieldsets' => [
                        'size' => [
                            'title'   => 'Vipimo vya kifurushi',
                            'entries' => [
                                'length' => 'Urefu',
                                'width'  => 'Upana',
                                'height' => 'Kimo',
                            ],
                        ],
                    ],
                    'weight'     => 'Uzito wa msingi',
                    'max-weight' => 'Uzito wa juu kabisa',
                    'barcode'    => 'Msimbopau',
                    'company'    => 'Kampuni',
                    'created-at' => 'Iliundwa',
                    'updated-at' => 'Ilisasishwa mwisho',
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
