<?php

return [
    'title' => 'Aina za shughuli',

    'navigation' => [
        'title' => 'Aina za shughuli',
        'group' => 'Wafanyakazi',
    ],

    'form' => [
        'sections' => [
            'activity-type-details' => [
                'title' => 'Taarifa za jumla',

                'fields' => [
                    'name'                => 'Aina ya shughuli',
                    'name-tooltip'        => 'Weka jina rasmi la aina ya shughuli',
                    'action'              => 'Kitendo',
                    'default-user'        => 'Mtumiaji chaguo-msingi',
                    'summary'             => 'Muhtasari',
                    'note'                => 'Kidokezo',
                ],
            ],

            'delay-information' => [
                'title' => 'Taarifa za kuchelewa',

                'fields' => [
                    'delay-count'            => 'Idadi ya kuchelewa',
                    'delay-unit'             => 'Kipimo cha kuchelewa',
                    'delay-form'             => 'Ucheleweshaji kuanzia',
                    'delay-form-helper-text' => 'Chanzo cha kukokotoa kuchelewa',
                ],
            ],

            'advanced-information' => [
                'title' => 'Taarifa za ziada',

                'fields' => [
                    'icon'                => 'Aikoni',
                    'decoration-type'     => 'Aina ya mapambo',
                    'chaining-type'       => 'Aina ya mfuatano',
                    'suggest'             => 'Pendekeza',
                    'trigger'             => 'Anzisha',
                ],
            ],

            'status-and-configuration-information' => [
                'title' => 'Hali na mipangilio',

                'fields' => [
                    'status'               => 'Hali',
                    'keep-done-activities' => 'Hifadhi shughuli zilizokamilika',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'       => 'Aina ya shughuli',
            'summary'    => 'Muhtasari',
            'planned-in' => 'Imepangwa ndani ya',
            'type'       => 'Aina',
            'action'     => 'Kitendo',
            'status'     => 'Hali',
            'created-at' => 'Iliundwa',
            'updated-at' => 'Ilisasishwa',
        ],

        'groups' => [
            'name'             => 'Jina',
            'action-category'  => 'Kategoria ya kitendo',
            'status'           => 'Hali',
            'delay-count'      => 'Idadi ya kuchelewa',
            'delay-unit'       => 'Kipimo cha kuchelewa',
            'delay-source'     => 'Chanzo cha kuchelewa',
            'associated-model' => 'Modeli husika',
            'chaining-type'    => 'Aina ya mfuatano',
            'decoration-type'  => 'Aina ya mapambo',
            'default-user'     => 'Mtumiaji chaguo-msingi',
            'creation-date'    => 'Tarehe ya kuundwa',
            'last-update'      => 'Sasisho la mwisho',
        ],

        'filters' => [
            'action'    => 'Kitendo',
            'status'    => 'Hali',
            'has-delay' => 'Ina kuchelewa',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Aina ya shughuli imerejeshwa',
                    'body'  => 'Aina ya shughuli imerejeshwa kikamilifu.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Aina ya shughuli imefutwa',
                    'body'  => 'Aina ya shughuli imefutwa kikamilifu.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Aina ya shughuli imefutwa kabisa',
                        'body'  => 'Aina ya shughuli imefutwa kabisa.',
                    ],
                    'error' => [
                        'title' => 'Aina ya shughuli haikuweza kufutwa',
                        'body'  => 'Aina ya shughuli haiwezi kufutwa kwa sababu inatumika kwa sasa.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Aina za shughuli zimerejeshwa',
                    'body'  => 'Aina za shughuli zimerejeshwa kikamilifu.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Aina za shughuli zimefutwa',
                    'body'  => 'Aina za shughuli zimefutwa kikamilifu.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Aina za shughuli zimefutwa kabisa',
                    'body'  => 'Aina za shughuli zimefutwa kabisa.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'activity-type-details' => [
                'title' => 'Taarifa za jumla',

                'entries' => [
                    'name'                => 'Aina ya shughuli',
                    'name-tooltip'        => 'Weka jina rasmi la aina ya shughuli',
                    'action'              => 'Kitendo',
                    'default-user'        => 'Mtumiaji chaguo-msingi',
                    'plugin'              => 'Programu-jalizi',
                    'summary'             => 'Muhtasari',
                    'note'                => 'Kidokezo',
                ],
            ],

            'delay-information' => [
                'title' => 'Taarifa za kuchelewa',

                'entries' => [
                    'delay-count'            => 'Idadi ya kuchelewa',
                    'delay-unit'             => 'Kipimo cha kuchelewa',
                    'delay-form'             => 'Ucheleweshaji kuanzia',
                    'delay-form-helper-text' => 'Chanzo cha kukokotoa kuchelewa',
                ],
            ],

            'advanced-information' => [
                'title' => 'Taarifa za ziada',

                'entries' => [
                    'icon'                => 'Aikoni',
                    'decoration-type'     => 'Aina ya mapambo',
                    'chaining-type'       => 'Aina ya mfuatano',
                    'suggest'             => 'Pendekeza',
                    'trigger'             => 'Anzisha',
                ],
            ],

            'status-and-configuration-information' => [
                'title' => 'Hali na mipangilio',

                'entries' => [
                    'status'               => 'Hali',
                    'keep-done-activities' => 'Hifadhi shughuli zilizokamilika',
                ],
            ],
        ],
    ],
];
