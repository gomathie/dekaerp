<?php

return [
    'navigation' => [
        'title' => 'Maeneo',
        'group' => 'Ghala Management',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Jumla',

                'fields' => [
                    'location'                     => 'Mahali',
                    'location-placeholder'         => 'eg. Spare Hisa',
                    'parent-location'              => 'Parent Mahali',
                    'parent-location-hint-tooltip' => 'main mahali hiyo encompasses hii mahali. Kwa mfano, \'Dispatch Zone\' ni part ya \'Gate 1\' parent mahali.',
                    'external-notes'               => 'Nje Maelezo',
                ],
            ],

            'settings' => [
                'title'  => 'Mipangilio',

                'fields' => [
                    'location-type'                 => 'Mahali Aina',
                    'company'                       => 'Kampuni',
                    'storage-category'              => 'Storage Kategoria',
                    'is-scrap'                      => 'Ni Scrap Mahali?',
                    'is-scrap-hint-tooltip'         => 'Chagua hii checkbox hadi designate hii mahali kwa storing scrapped au damaged goods.',
                    'is-dock'                       => 'Ni Dock Mahali?',
                    'is-dock-hint-tooltip'          => 'Chagua hii checkbox hadi designate hii mahali kwa storing goods hiyo ni ready kwa shehena.',
                    'is-replenish'                  => 'Ni Replenish Mahali?',
                    'is-replenish-hint-tooltip'     => 'Washa hii function hadi retrieve zote quantities needed kwa replenishment saa hii mahali.',
                    'logistics'                     => 'Usafirishaji',
                    'removal-strategy'              => 'Removal Strategy',
                    'removal-strategy-hint-tooltip' => 'Specifies chaguo-msingi method kwa determining exact shelf, kundi, na mahali kutoka ambayo hadi pick bidhaa. Hii method unaweza kuwa enforced saa bidhaa kategoria level, na fallback hadi parent maeneo ikiwa si weka hapa.',
                    'cyclic-counting'               => 'Cyclic Counting',
                    'inventory-frequency'           => 'Ghala Frequency',
                    'last-inventory'                => 'Mwisho Ghala',
                    'last-inventory-hint-tooltip'   => 'Tarehe ya mwisho ghala saa hii mahali.',
                    'next-expected'                 => 'Ifuatayo inatarajiwa',
                    'next-expected-hint-tooltip'    => 'Tarehe kwa ifuatayo imepangwa ghala based washa cyclic ratiba.',
                ],
            ],

            'additional' => [
                'title'  => 'Additional Taarifa',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'location'         => 'Mahali',
            'type'             => 'Aina',
            'storage-category' => 'Storage Kategoria',
            'company'          => 'Kampuni',
            'deleted-at'       => 'imefutwa',
            'created-at'       => 'Imeundwa',
            'updated-at'       => 'Imesasishwa',
        ],

        'groups' => [
            'warehouse'       => 'Ghala',
            'type'            => 'Aina',
            'created-at'      => 'Imeundwa',
            'updated-at'      => 'Imesasishwa',
        ],

        'filters' => [
            'location' => 'Mahali',
            'type'     => 'Aina',
            'company'  => 'Kampuni',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Mahali imesasishwa',
                    'body'  => 'mahali ina imesasishwa successfully.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Mahali restored',
                    'body'  => 'mahali ina restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Mahali imefutwa',
                    'body'  => 'mahali ina imefutwa successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Mahali force imefutwa',
                        'body'  => 'mahali ina force imefutwa successfully.',
                    ],

                    'error' => [
                        'title' => 'Mahali could si kuwa imefutwa',
                        'body'  => 'mahali haiwezekani kuwa imefutwa kwa sababu hii ni currently katika tumia.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'print' => [
                'label' => 'Chapisha Msimbo pau',
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Maeneo restored',
                    'body'  => 'maeneo ina restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Maeneo imefutwa',
                    'body'  => 'maeneo ina imefutwa successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Maeneo force imefutwa',
                        'body'  => 'maeneo ina force imefutwa successfully.',
                    ],

                    'error' => [
                        'title' => 'Maeneo could si kuwa imefutwa',
                        'body'  => 'maeneo haiwezekani kuwa imefutwa kwa sababu wao ni currently katika tumia.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'  => 'Jumla',

                'entries' => [
                    'location'                     => 'Mahali',
                    'location-placeholder'         => 'eg. Spare Hisa',
                    'parent-location'              => 'Parent Mahali',
                    'parent-location-hint-tooltip' => 'main mahali hiyo encompasses hii mahali. Kwa mfano, \'Dispatch Zone\' ni part ya \'Gate 1\' parent mahali.',
                    'external-notes'               => 'Nje Maelezo',
                ],
            ],

            'settings' => [
                'title'  => 'Mipangilio',

                'entries' => [
                    'location-type'                 => 'Mahali Aina',
                    'company'                       => 'Kampuni',
                    'storage-category'              => 'Storage Kategoria',
                    'is-scrap'                      => 'Ni Scrap Mahali?',
                    'is-scrap-hint-tooltip'         => 'Chagua hii checkbox hadi designate hii mahali kwa storing scrapped au damaged goods.',
                    'is-dock'                       => 'Ni Dock Mahali?',
                    'is-dock-hint-tooltip'          => 'Chagua hii checkbox hadi designate hii mahali kwa storing goods hiyo ni ready kwa shehena.',
                    'is-replenish'                  => 'Ni Replenish Mahali?',
                    'is-replenish-hint-tooltip'     => 'Washa hii function hadi retrieve zote quantities needed kwa replenishment saa hii mahali.',
                    'logistics'                     => 'Usafirishaji',
                    'removal-strategy'              => 'Removal Strategy',
                    'removal-strategy-hint-tooltip' => 'Specifies chaguo-msingi method kwa determining exact shelf, kundi, na mahali kutoka ambayo hadi pick bidhaa. Hii method unaweza kuwa enforced saa bidhaa kategoria level, na fallback hadi parent maeneo ikiwa si weka hapa.',
                    'cyclic-counting'               => 'Cyclic Counting',
                    'inventory-frequency'           => 'Ghala Frequency',
                    'last-inventory'                => 'Mwisho Ghala',
                    'last-inventory-hint-tooltip'   => 'Tarehe ya mwisho ghala saa hii mahali.',
                    'next-expected'                 => 'Ifuatayo inatarajiwa',
                    'next-expected-hint-tooltip'    => 'Tarehe kwa ifuatayo imepangwa ghala based washa cyclic ratiba.',
                ],
            ],

            'additional' => [
                'title'  => 'Additional Taarifa',
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
