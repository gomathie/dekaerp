<?php

return [
    'navigation' => [
        'title' => 'Operesheni',
        'group' => 'Mipangilio',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Jumla',
                'fields' => [
                    'name'              => 'Operesheni',
                    'name-placeholder'  => 'mf. Kukata',
                    'bill-of-material'  => 'Orodha ya malighafi',
                    'work-center'       => 'Kituo cha kazi',
                    'apply-on-variants' => 'Tumia kwenye aina za bidhaa',
                    'company'           => 'Kampuni',
                    'blocked-by'        => 'Imezuiwa na',
                ],
            ],
            'settings' => [
                'title'  => 'Mipangilio',
                'fields' => [
                    'time-mode'                  => 'Ukokotoaji wa muda',
                    'time-mode-batch'            => 'Kulingana na',
                    'time-mode-batch-prefix'     => 'Oda za kazi',
                    'time-mode-batch-suffix'     => 'za mwisho',
                    'manual-cycle-time'          => 'Muda chaguo-msingi',
                    'manual-cycle-time-suffix'   => 'dakika',
                ],
            ],
            'worksheet' => [
                'title'  => 'Karatasi ya kazi',
                'fields' => [
                    'worksheet'                => 'Karatasi ya kazi',
                    'pdf'                      => 'PDF',
                    'google-slide'             => 'Google Slides',
                    'google-slide-placeholder' => 'Kiungo cha Google Slides',
                    'description'              => 'Maelezo',
                    'description-placeholder'  => 'Maelezo ya operesheni...',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'              => 'Operesheni',
            'bill-of-material'  => 'Orodha ya malighafi',
            'work-center'       => 'Kituo cha kazi',
            'time-mode'         => 'Ukokotoaji wa muda',
            'manual-cycle-time' => 'Muda chaguo-msingi',
            'worksheet-type'    => 'Karatasi ya kazi',
            'deleted-at'        => 'Ilifutwa',
            'created-at'        => 'Iliundwa',
            'updated-at'        => 'Ilisasishwa',
        ],
        'filters' => [
            'work-center'    => 'Kituo cha kazi',
            'time-mode'      => 'Ukokotoaji wa muda',
            'worksheet-type' => 'Karatasi ya kazi',
        ],
        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Operesheni imerejeshwa',
                    'body'  => 'Operesheni imerejeshwa kikamilifu.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Operesheni imewekwa kwenye kumbukumbu',
                    'body'  => 'Operesheni imewekwa kwenye kumbukumbu kikamilifu.',
                ],
            ],
            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Operesheni imefutwa',
                        'body'  => 'Operesheni imefutwa kabisa.',
                    ],
                    'error' => [
                        'title' => 'Operesheni haikuweza kufutwa',
                        'body'  => 'Operesheni haiwezi kufutwa kwa sababu inatumika kwa sasa.',
                    ],
                ],
            ],
        ],
        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Operesheni zimerejeshwa',
                    'body'  => 'Operesheni zilizochaguliwa zimerejeshwa kikamilifu.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Operesheni zimewekwa kwenye kumbukumbu',
                    'body'  => 'Operesheni zilizochaguliwa zimewekwa kwenye kumbukumbu kikamilifu.',
                ],
            ],
            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Operesheni zimefutwa',
                        'body'  => 'Operesheni zilizochaguliwa zimefutwa kabisa.',
                    ],
                    'error' => [
                        'title' => 'Operesheni hazikuweza kufutwa',
                        'body'  => 'Operesheni moja au zaidi zilizochaguliwa zinatumika kwa sasa.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'   => 'Taarifa za jumla',
                'entries' => [
                    'name'              => 'Operesheni',
                    'bill-of-material'  => 'Orodha ya malighafi',
                    'work-center'       => 'Kituo cha kazi',
                    'apply-on-variants' => 'Tumia kwenye aina za bidhaa',
                    'company'           => 'Kampuni',
                ],
            ],
            'settings' => [
                'title'   => 'Mipangilio',
                'entries' => [
                    'time-mode'                => 'Ukokotoaji wa muda',
                    'time-mode-batch'          => 'Kulingana na',
                    'manual-cycle-time'        => 'Muda chaguo-msingi',
                    'manual-cycle-time-suffix' => 'dakika',
                ],
            ],
            'worksheet' => [
                'title'   => 'Karatasi ya kazi',
                'entries' => [
                    'worksheet'    => 'Karatasi ya kazi',
                    'pdf'          => 'PDF',
                    'google-slide' => 'Google Slides',
                    'description'  => 'Maelezo',
                ],
            ],
            'record-information' => [
                'title'   => 'Taarifa za rekodi',
                'entries' => [
                    'created-by'   => 'Imeundwa na',
                    'created-at'   => 'Iliundwa',
                    'last-updated' => 'Ilisasishwa mwisho',
                ],
            ],
        ],
    ],
];
