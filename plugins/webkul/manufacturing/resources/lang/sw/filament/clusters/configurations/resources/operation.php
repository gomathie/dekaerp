<?php

return [
    'navigation' => [
        'title' => 'Shughuli',
        'group' => 'Usanidi',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Jumla',
                'fields' => [
                    'name'              => 'Operation',
                    'name-placeholder'  => 'eg. Cutting',
                    'bill-of-material'  => 'Bili ya Material',
                    'work-center'       => 'Work Center',
                    'apply-on-variants' => 'Tumia washa Variants',
                    'company'           => 'Kampuni',
                    'blocked-by'        => 'Blocked Na',
                ],
            ],
            'settings' => [
                'title'  => 'Mipangilio',
                'fields' => [
                    'time-mode'                  => 'Duration Computation',
                    'time-mode-batch'            => 'Based Washa',
                    'time-mode-batch-prefix'     => 'mwisho',
                    'time-mode-batch-suffix'     => 'agizo la kazis',
                    'manual-cycle-time'          => 'Chaguo-msingi Duration',
                    'manual-cycle-time-suffix'   => 'dakika',
                ],
            ],
            'worksheet' => [
                'title'  => 'Work Sheet',
                'fields' => [
                    'worksheet'                => 'Worksheet',
                    'pdf'                      => 'PDF',
                    'google-slide'             => 'Google Slide',
                    'google-slide-placeholder' => 'Google Slide Link',
                    'description'              => 'Maelezo',
                    'description-placeholder'  => 'Maelezo ya operation...',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'              => 'Operation',
            'bill-of-material'  => 'Bili ya Material',
            'work-center'       => 'Work Center',
            'time-mode'         => 'Duration Computation',
            'manual-cycle-time' => 'Chaguo-msingi Duration',
            'worksheet-type'    => 'Worksheet',
            'deleted-at'        => 'Imefutwa',
            'created-at'        => 'Imeundwa',
            'updated-at'        => 'Imesasishwa',
        ],
        'filters' => [
            'work-center'    => 'Work Center',
            'time-mode'      => 'Duration Computation',
            'worksheet-type' => 'Worksheet',
        ],
        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Operation restored',
                    'body'  => 'operation ina restored successfully.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Operation imehifadhiwa',
                    'body'  => 'operation ina imehifadhiwa successfully.',
                ],
            ],
            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Operation imefutwa',
                        'body'  => 'operation ina permanently imefutwa.',
                    ],
                    'error' => [
                        'title' => 'Operation could si kuwa imefutwa',
                        'body'  => 'operation haiwezekani kuwa imefutwa kwa sababu hii ni currently katika tumia.',
                    ],
                ],
            ],
        ],
        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Shughuli restored',
                    'body'  => 'imechaguliwa shughuli ina restored successfully.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Shughuli imehifadhiwa',
                    'body'  => 'imechaguliwa shughuli ina imehifadhiwa successfully.',
                ],
            ],
            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Shughuli imefutwa',
                        'body'  => 'imechaguliwa shughuli ina permanently imefutwa.',
                    ],
                    'error' => [
                        'title' => 'Shughuli could si kuwa imefutwa',
                        'body'  => 'One au zaidi imechaguliwa shughuli ni currently katika tumia.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'   => 'Jumla Taarifa',
                'entries' => [
                    'name'              => 'Operation',
                    'bill-of-material'  => 'Bili ya Material',
                    'work-center'       => 'Work Center',
                    'apply-on-variants' => 'Tumia washa Variants',
                    'company'           => 'Kampuni',
                ],
            ],
            'settings' => [
                'title'   => 'Mipangilio',
                'entries' => [
                    'time-mode'                => 'Duration Computation',
                    'time-mode-batch'          => 'Based Washa',
                    'manual-cycle-time'        => 'Chaguo-msingi Duration',
                    'manual-cycle-time-suffix' => 'dakika',
                ],
            ],
            'worksheet' => [
                'title'   => 'Work Sheet',
                'entries' => [
                    'worksheet'    => 'Worksheet',
                    'pdf'          => 'PDF',
                    'google-slide' => 'Google Slide',
                    'description'  => 'Maelezo',
                ],
            ],
            'record-information' => [
                'title'   => 'Rekodi Taarifa',
                'entries' => [
                    'created-by'   => 'Imeundwa Na',
                    'created-at'   => 'Imeundwa',
                    'last-updated' => 'Mwisho Imesasishwa',
                ],
            ],
        ],
    ],
];
