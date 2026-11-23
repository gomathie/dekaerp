<?php

return [
    'navigation' => [
        'group' => 'Matengenezo',
        'title' => 'Matengenezo Requests',
    ],

    'form' => [
        'sections' => [
            'request' => [
                'title'  => 'Request',
                'fields' => [
                    'name'                      => 'Request',
                    'name-placeholder'          => 'e.g. Screen si working',
                    'equipment'                 => 'Equipment',
                    'category'                  => 'Kategoria',
                    'requested-at'              => 'Request Tarehe',
                    'requested-at-hint-tooltip' => 'tarehe matengenezo request ilikuwa reported.',
                    'maintenance-type'          => 'Matengenezo Aina',
                    'recurrent'                 => 'Recurrent',
                    'repeat-every'              => 'Repeat Kila',
                    'maintenance-type-options'  => [
                        'corrective' => 'Corrective',
                        'preventive' => 'Preventive',
                    ],
                ],
                'tabs' => [
                    'notes' => [
                        'title'  => 'Maelezo',
                        'fields' => [
                            'description'             => 'Ndani Maelezo',
                            'description-placeholder' => 'Ndani maelezo',
                        ],
                    ],
                    'instructions' => [
                        'title'  => 'Instructions',
                        'fields' => [
                            'instruction-type'         => 'Instruction Aina',
                            'instruction-type-options' => [
                                'pdf'          => 'PDF',
                                'google-slide' => 'Google Slide',
                                'text'         => 'Text',
                            ],
                            'instruction-pdf'              => 'PDF',
                            'instruction-google-slide'     => 'Google Slide',
                            'instruction-text'             => 'Maelezo',
                            'instruction-text-placeholder' => 'Maelezo',
                        ],
                    ],
                ],
            ],

            'settings' => [
                'title'  => 'Mipangilio',
                'fields' => [
                    'team'                      => 'Timu',
                    'responsible'               => 'Responsible',
                    'scheduled-at'              => 'Scheduled Tarehe',
                    'scheduled-at-hint-tooltip' => 'tarehe na time hii matengenezo work ni imepangwa hadi anza.',
                    'duration'                  => 'Duration',
                    'duration-hint-tooltip'     => 'Inatarajiwa matengenezo duration.',
                    'duration-suffix'           => 'saa',
                    'priority'                  => 'Kipaumbele',
                    'company'                   => 'Kampuni',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'       => 'Subjects',
            'creator'    => 'Imeundwa na Mtumiaji',
            'technician' => 'Technician',
            'category'   => 'Kategoria',
            'stage'      => 'Hatua',
            'company'    => 'Kampuni',
        ],

        'groups' => [
            'stage'       => 'Hatua',
            'assigned-to' => 'Amekabidhiwa hadi',
            'category'    => 'Kategoria',
            'created-by'  => 'Imeundwa Na',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Matengenezo request restored',
                    'body'  => 'matengenezo request ina restored successfully.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Matengenezo request imehifadhiwa',
                    'body'  => 'matengenezo request ina imehifadhiwa successfully.',
                ],
            ],
            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Matengenezo request imefutwa',
                        'body'  => 'matengenezo request ina permanently imefutwa.',
                    ],
                    'error' => [
                        'title' => 'Matengenezo request could si kuwa imefutwa',
                        'body'  => 'Hii matengenezo request ni referenced na another rekodi.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Matengenezo requests restored',
                    'body'  => 'imechaguliwa matengenezo requests ina restored successfully.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Matengenezo requests imehifadhiwa',
                    'body'  => 'imechaguliwa matengenezo requests ina imehifadhiwa successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'request' => [
                'title'   => 'Request',
                'entries' => [
                    'name'                     => 'Request',
                    'equipment'                => 'Equipment',
                    'category'                 => 'Kategoria',
                    'requested-at'             => 'Request Tarehe',
                    'maintenance-type'         => 'Matengenezo Aina',
                    'instruction-type'         => 'Instruction Aina',
                    'instruction-pdf'          => 'PDF',
                    'instruction-google-slide' => 'Google Slide',
                    'description'              => 'Ndani Maelezo',
                    'instruction-text'         => 'Maelezo',
                ],
            ],

            'settings' => [
                'title'   => 'Mipangilio',
                'entries' => [
                    'team'            => 'Timu',
                    'responsible'     => 'Responsible',
                    'scheduled-at'    => 'Scheduled Tarehe',
                    'duration'        => 'Duration',
                    'duration-suffix' => 'saa',
                    'priority'        => 'Kipaumbele',
                    'company'         => 'Kampuni',
                ],
            ],
        ],
    ],
];
