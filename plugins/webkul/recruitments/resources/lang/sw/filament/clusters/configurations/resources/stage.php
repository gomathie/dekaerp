<?php

return [
    'title' => 'Hatua',

    'navigation' => [
        'title' => 'Hatua',
        'group' => 'Kazi Positions',
    ],

    'form' => [
        'sections' => [
            'general-information' => [
                'title' => 'Jumla Taarifa',

                'fields' => [
                    'stage-name'   => 'Hatua Jina',
                    'sort'         => 'Mfuatano Agizo',
                    'requirements' => 'Requirements',
                ],
            ],

            'tooltips' => [
                'title'       => 'Tooltips',
                'description' => 'Define maalum lebo kwa application hali.',

                'fields' => [
                    'gray-label'          => 'Gray Lebo',
                    'gray-label-tooltip'  => 'lebo kwa gray hali.',
                    'red-label'           => 'Red Lebo',
                    'red-label-tooltip'   => 'lebo kwa red hali.',
                    'green-label'         => 'Green Lebo',
                    'green-label-tooltip' => 'lebo kwa green hali.',
                ],
            ],

            'additional-information' => [
                'title' => 'Additional Taarifa',

                'fields' => [
                    'job-positions' => 'Kazi Positions',
                    'folded'        => 'Folded',
                    'hired-stage'   => 'Hired Hatua',
                    'default-stage' => 'Chaguo-msingi Hatua',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'id'                 => 'Kitambulisho',
            'name'               => 'Hatua Jina',
            'hired-stage'        => 'Hired Hatua',
            'default-stage'      => 'Chaguo-msingi Hatua',
            'folded'             => 'Folded',
            'job-positions'      => 'Kazi Positions',
            'created-by'         => 'Imeundwa Na',
            'created-at'         => 'Imeundwa',
            'updated-at'         => 'Imesasishwa',
        ],

        'filters' => [
            'name'         => 'Hatua Jina',
            'job-position' => 'Kazi Cheo',
            'folded'       => 'Folded',
            'gray-label'   => 'Gray Lebo',
            'red-label'    => 'Red Lebo',
            'green-label'  => 'Green Lebo',
            'created-by'   => 'Imeundwa Na',
            'created-at'   => 'Imeundwa',
            'updated-at'   => 'Imesasishwa',
        ],

        'groups' => [
            'job-position' => 'Kazi Cheo',
            'stage-name'   => 'Hatua Jina',
            'folded'       => 'Folded',
            'gray-label'   => 'Gray Lebo',
            'red-label'    => 'Red Lebo',
            'green-label'  => 'Green Lebo',
            'created-by'   => 'Imeundwa Na',
            'created-at'   => 'Imeundwa',
            'updated-at'   => 'Imesasishwa',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Hatua imefutwa',
                        'body'  => 'Hatua ina imefutwa successfully.',
                    ],

                    'error' => [
                        'title' => 'Hatua could si kuwa imefutwa',
                        'body'  => 'Hatua haiwezekani kuwa imefutwa kwa sababu hii ni currently katika tumia.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Hatua imefutwa',
                    'body'  => 'Hatua ina imefutwa successfully.',
                ],
            ],
        ],

        'empty-state-actions' => [
            'create' => [
                'label' => 'Mpya Hatua',
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general-information' => [
                'title' => 'Jumla Taarifa',

                'entries' => [
                    'stage-name'   => 'Hatua Jina',
                    'sort'         => 'Mfuatano Agizo',
                    'requirements' => 'Requirements',
                ],
            ],

            'tooltips' => [
                'title'       => 'Tooltips',
                'description' => 'Define maalum lebo kwa application hali.',

                'entries' => [
                    'gray-label'          => 'Gray Lebo',
                    'gray-label-tooltip'  => 'lebo kwa gray hali.',
                    'red-label'           => 'Red Lebo',
                    'red-label-tooltip'   => 'lebo kwa red hali.',
                    'green-label'         => 'Green Lebo',
                    'green-label-tooltip' => 'lebo kwa green hali.',
                ],
            ],

            'additional-information' => [
                'title' => 'Additional Taarifa',

                'entries' => [
                    'job-positions'      => 'Kazi Cheo',
                    'folded'             => 'Folded',
                    'hired-stage'        => 'Hired Hatua',
                    'default-stage'      => 'Chaguo-msingi Hatua',
                ],
            ],
        ],
    ],

];
