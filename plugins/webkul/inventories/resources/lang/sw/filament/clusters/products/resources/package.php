<?php

return [
    'navigation' => [
        'title' => 'Vifurushi',
        'group' => 'Ghala',
    ],

    'global-search' => [
        'name'         => 'Jina',
        'package-type' => 'Kifurushi Aina',
        'location'     => 'Mahali',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Jumla',

                'fields' => [
                    'name'             => 'Jina',
                    'name-placeholder' => 'eg. PACK007',
                    'package-type'     => 'Kifurushi Aina',
                    'pack-date'        => 'Pack Tarehe',
                    'location'         => 'Mahali',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'         => 'Jina',
            'package-type' => 'Kifurushi Aina',
            'location'     => 'Mahali',
            'company'      => 'Kampuni',
            'created-at'   => 'Imeundwa',
            'updated-at'   => 'Imesasishwa',
        ],

        'groups' => [
            'package-type'   => 'Kifurushi Aina',
            'location'       => 'Mahali',
            'created-at'     => 'Imeundwa',
        ],

        'filters' => [
            'package-type' => 'Kifurushi Aina',
            'location'     => 'Mahali',
            'creator'      => 'Creator',
            'company'      => 'Kampuni',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Kifurushi imefutwa',
                        'body'  => 'kifurushi ina imefutwa successfully.',
                    ],

                    'error' => [
                        'title' => 'Kifurushi could si kuwa imefutwa',
                        'body'  => 'kifurushi haiwezekani kuwa imefutwa kwa sababu hii ni currently katika tumia.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'print-without-content' => [
                'label' => 'Chapisha Msimbo pau',
            ],

            'print-with-content' => [
                'label' => 'Chapisha Msimbo pau Na Content',
            ],

            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Vifurushi imefutwa',
                        'body'  => 'vifurushi ina imefutwa successfully.',
                    ],

                    'error' => [
                        'title' => 'Vifurushi could si kuwa imefutwa',
                        'body'  => 'vifurushi haiwezekani kuwa imefutwa kwa sababu wao ni currently katika tumia.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Kifurushi Maelezo',

                'entries' => [
                    'name'         => 'Kifurushi Jina',
                    'package-type' => 'Kifurushi Aina',
                    'pack-date'    => 'Pack Tarehe',
                    'location'     => 'Mahali',
                    'company'      => 'Kampuni',
                    'created-at'   => 'Imeundwa',
                    'updated-at'   => 'Mwisho Imesasishwa',
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
