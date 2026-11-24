<?php

return [
    'title' => 'Hatua',

    'navigation' => [
        'title' => 'Hatua',
        'group' => 'Nafasi za kazi',
    ],

    'form' => [
        'sections' => [
            'general-information' => [
                'title' => 'Taarifa za jumla',

                'fields' => [
                    'stage-name'   => 'Jina la hatua',
                    'sort'         => 'Mpangilio',
                    'requirements' => 'Mahitaji',
                ],
            ],

            'tooltips' => [
                'title'       => 'Vidokezo vya msaada',
                'description' => 'Weka lebo maalum za hali ya ombi.',

                'fields' => [
                    'gray-label'          => 'Lebo ya kijivu',
                    'gray-label-tooltip'  => 'Lebo ya hali ya kijivu.',
                    'red-label'           => 'Lebo nyekundu',
                    'red-label-tooltip'   => 'Lebo ya hali nyekundu.',
                    'green-label'         => 'Lebo ya kijani',
                    'green-label-tooltip' => 'Lebo ya hali ya kijani.',
                ],
            ],

            'additional-information' => [
                'title' => 'Taarifa za ziada',

                'fields' => [
                    'job-positions' => 'Nafasi za kazi',
                    'folded'        => 'Imekunjwa',
                    'hired-stage'   => 'Hatua ya kuajiriwa',
                    'default-stage' => 'Hatua chaguo-msingi',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'id'                 => 'ID',
            'name'               => 'Jina la hatua',
            'hired-stage'        => 'Hatua ya kuajiriwa',
            'default-stage'      => 'Hatua chaguo-msingi',
            'folded'             => 'Imekunjwa',
            'job-positions'      => 'Nafasi za kazi',
            'created-by'         => 'Imeundwa na',
            'created-at'         => 'Tarehe ya kuundwa',
            'updated-at'         => 'Tarehe ya kusasishwa',
        ],

        'filters' => [
            'name'         => 'Jina la hatua',
            'job-position' => 'Nafasi ya kazi',
            'folded'       => 'Imekunjwa',
            'gray-label'   => 'Lebo ya kijivu',
            'red-label'    => 'Lebo nyekundu',
            'green-label'  => 'Lebo ya kijani',
            'created-by'   => 'Imeundwa na',
            'created-at'   => 'Tarehe ya kuundwa',
            'updated-at'   => 'Tarehe ya kusasishwa',
        ],

        'groups' => [
            'job-position' => 'Nafasi ya kazi',
            'stage-name'   => 'Jina la hatua',
            'folded'       => 'Imekunjwa',
            'gray-label'   => 'Lebo ya kijivu',
            'red-label'    => 'Lebo nyekundu',
            'green-label'  => 'Lebo ya kijani',
            'created-by'   => 'Imeundwa na',
            'created-at'   => 'Tarehe ya kuundwa',
            'updated-at'   => 'Tarehe ya kusasishwa',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Hatua zimefutwa',
                        'body'  => 'Hatua zimefutwa kwa mafanikio.',
                    ],

                    'error' => [
                        'title' => 'Hatua hazikuweza kufutwa',
                        'body'  => 'Hatua haziwezi kufutwa kwa sababu zinatumika.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Hatua zimefutwa',
                    'body'  => 'Hatua zimefutwa kwa mafanikio.',
                ],
            ],
        ],

        'empty-state-actions' => [
            'create' => [
                'label' => 'Hatua mpya',
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general-information' => [
                'title' => 'Taarifa za jumla',

                'entries' => [
                    'stage-name'   => 'Jina la hatua',
                    'sort'         => 'Mpangilio',
                    'requirements' => 'Mahitaji',
                ],
            ],

            'tooltips' => [
                'title'       => 'Vidokezo vya msaada',
                'description' => 'Weka lebo maalum za hali ya ombi.',

                'entries' => [
                    'gray-label'          => 'Lebo ya kijivu',
                    'gray-label-tooltip'  => 'Lebo ya hali ya kijivu.',
                    'red-label'           => 'Lebo nyekundu',
                    'red-label-tooltip'   => 'Lebo ya hali nyekundu.',
                    'green-label'         => 'Lebo ya kijani',
                    'green-label-tooltip' => 'Lebo ya hali ya kijani.',
                ],
            ],

            'additional-information' => [
                'title' => 'Taarifa za ziada',

                'entries' => [
                    'job-positions'      => 'Nafasi ya kazi',
                    'folded'             => 'Imekunjwa',
                    'hired-stage'        => 'Hatua ya kuajiriwa',
                    'default-stage'      => 'Hatua chaguo-msingi',
                ],
            ],
        ],
    ],

];
