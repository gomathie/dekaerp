<?php

return [
    'navigation' => [
        'title' => 'Vifurushi',
        'group' => 'Bidhaa ghalani',
    ],

    'global-search' => [
        'name'         => 'Jina',
        'package-type' => 'Aina ya kifurushi',
        'location'     => 'Eneo',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Jumla',

                'fields' => [
                    'name'             => 'Jina',
                    'name-placeholder' => 'mf. PACK007',
                    'package-type'     => 'Aina ya kifurushi',
                    'pack-date'        => 'Tarehe ya kufunga',
                    'location'         => 'Eneo',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'         => 'Jina',
            'package-type' => 'Aina ya kifurushi',
            'location'     => 'Eneo',
            'company'      => 'Kampuni',
            'created-at'   => 'Iliundwa',
            'updated-at'   => 'Ilisasishwa',
        ],

        'groups' => [
            'package-type'   => 'Aina ya kifurushi',
            'location'       => 'Eneo',
            'created-at'     => 'Iliundwa',
        ],

        'filters' => [
            'package-type' => 'Aina ya kifurushi',
            'location'     => 'Eneo',
            'creator'      => 'Muundaji',
            'company'      => 'Kampuni',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Kifurushi kimefutwa',
                        'body'  => 'Kifurushi kimefutwa kwa mafanikio.',
                    ],

                    'error' => [
                        'title' => 'Kifurushi hakikuweza kufutwa',
                        'body'  => 'Kifurushi hakiwezi kufutwa kwa sababu kinatumika.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'print-without-content' => [
                'label' => 'Chapisha msimbopau',
            ],

            'print-with-content' => [
                'label' => 'Chapisha msimbopau pamoja na yaliyomo',
            ],

            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Vifurushi vimefutwa',
                        'body'  => 'Vifurushi vimefutwa kwa mafanikio.',
                    ],

                    'error' => [
                        'title' => 'Vifurushi havikuweza kufutwa',
                        'body'  => 'Vifurushi haviwezi kufutwa kwa sababu vinatumika.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Maelezo ya kifurushi',

                'entries' => [
                    'name'         => 'Jina la kifurushi',
                    'package-type' => 'Aina ya kifurushi',
                    'pack-date'    => 'Tarehe ya kufunga',
                    'location'     => 'Eneo',
                    'company'      => 'Kampuni',
                    'created-at'   => 'Iliundwa',
                    'updated-at'   => 'Ilisasishwa mwisho',
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
