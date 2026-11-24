<?php

return [
    'navigation' => [
        'group' => 'Matengenezo',
        'title' => 'Maombi ya matengenezo',
    ],

    'form' => [
        'sections' => [
            'request' => [
                'title'  => 'Ombi',
                'fields' => [
                    'name'                      => 'Ombi',
                    'name-placeholder'          => 'k.m. Skrini haifanyi kazi',
                    'equipment'                 => 'Kifaa',
                    'category'                  => 'Kategoria',
                    'requested-at'              => 'Tarehe ya ombi',
                    'requested-at-hint-tooltip' => 'Tarehe ombi la matengenezo liliporipotiwa.',
                    'maintenance-type'          => 'Aina ya matengenezo',
                    'recurrent'                 => 'Linajirudia',
                    'repeat-every'              => 'Rudia kila',
                    'maintenance-type-options'  => [
                        'corrective' => 'Marekebisho',
                        'preventive' => 'Kinga',
                    ],
                ],
                'tabs' => [
                    'notes' => [
                        'title'  => 'Vidokezo',
                        'fields' => [
                            'description'             => 'Vidokezo vya ndani',
                            'description-placeholder' => 'Vidokezo vya ndani',
                        ],
                    ],
                    'instructions' => [
                        'title'  => 'Maelekezo',
                        'fields' => [
                            'instruction-type'         => 'Aina ya maelekezo',
                            'instruction-type-options' => [
                                'pdf'          => 'PDF',
                                'google-slide' => 'Google Slides',
                                'text'         => 'Maandishi',
                            ],
                            'instruction-pdf'              => 'PDF',
                            'instruction-google-slide'     => 'Google Slides',
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
                    'responsible'               => 'Mwajibikaji',
                    'scheduled-at'              => 'Tarehe iliyopangwa',
                    'scheduled-at-hint-tooltip' => 'Tarehe na saa ambapo kazi hii ya matengenezo imepangwa kuanza.',
                    'duration'                  => 'Muda',
                    'duration-hint-tooltip'     => 'Muda unaotarajiwa wa matengenezo.',
                    'duration-suffix'           => 'saa',
                    'priority'                  => 'Kipaumbele',
                    'company'                   => 'Kampuni',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'       => 'Mada',
            'creator'    => 'Imeundwa na mtumiaji',
            'technician' => 'Fundi',
            'category'   => 'Kategoria',
            'stage'      => 'Hatua',
            'company'    => 'Kampuni',
        ],

        'groups' => [
            'stage'       => 'Hatua',
            'assigned-to' => 'Amepewa',
            'category'    => 'Kategoria',
            'created-by'  => 'Imeundwa na',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Ombi la matengenezo limerejeshwa',
                    'body'  => 'Ombi la matengenezo limerejeshwa kwa mafanikio.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Ombi la matengenezo limewekwa kumbukumbu',
                    'body'  => 'Ombi la matengenezo limewekwa kumbukumbu kwa mafanikio.',
                ],
            ],
            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Ombi la matengenezo limefutwa',
                        'body'  => 'Ombi la matengenezo limefutwa kabisa.',
                    ],
                    'error' => [
                        'title' => 'Ombi la matengenezo halikuweza kufutwa',
                        'body'  => 'Ombi hili la matengenezo linatumiwa na rekodi nyingine.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Maombi ya matengenezo yamerejeshwa',
                    'body'  => 'Maombi ya matengenezo yaliyochaguliwa yamerejeshwa kwa mafanikio.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Maombi ya matengenezo yamewekwa kumbukumbu',
                    'body'  => 'Maombi ya matengenezo yaliyochaguliwa yamewekwa kumbukumbu kwa mafanikio.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'request' => [
                'title'   => 'Ombi',
                'entries' => [
                    'name'                     => 'Ombi',
                    'equipment'                => 'Kifaa',
                    'category'                 => 'Kategoria',
                    'requested-at'             => 'Tarehe ya ombi',
                    'maintenance-type'         => 'Aina ya matengenezo',
                    'instruction-type'         => 'Aina ya maelekezo',
                    'instruction-pdf'          => 'PDF',
                    'instruction-google-slide' => 'Google Slides',
                    'description'              => 'Vidokezo vya ndani',
                    'instruction-text'         => 'Maelezo',
                ],
            ],

            'settings' => [
                'title'   => 'Mipangilio',
                'entries' => [
                    'team'            => 'Timu',
                    'responsible'     => 'Mwajibikaji',
                    'scheduled-at'    => 'Tarehe iliyopangwa',
                    'duration'        => 'Muda',
                    'duration-suffix' => 'saa',
                    'priority'        => 'Kipaumbele',
                    'company'         => 'Kampuni',
                ],
            ],
        ],
    ],
];
