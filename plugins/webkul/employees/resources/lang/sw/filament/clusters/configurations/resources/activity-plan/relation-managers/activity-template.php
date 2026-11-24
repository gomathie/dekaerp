<?php

return [
    'form' => [
        'sections' => [
            'activity-details' => [
                'title' => 'Maelezo ya shughuli',

                'fields' => [
                    'activity-type' => 'Aina ya shughuli',
                    'summary'       => 'Muhtasari',
                    'note'          => 'Kidokezo',
                ],
            ],

            'assignment' => [
                'title' => 'Ukabidhiwaji',

                'fields' => [
                    'assignment' => 'Ukabidhiwaji',
                    'assignee'   => 'Mkabidhiwa',
                ],
            ],

            'delay-information' => [
                'title' => 'Taarifa za ucheleweshaji',

                'fields' => [
                    'delay-count'            => 'Idadi ya ucheleweshaji',
                    'delay-unit'             => 'Kipimo cha ucheleweshaji',
                    'delay-from'             => 'Ucheleweshaji kuanzia',
                    'delay-from-helper-text' => 'Chanzo cha kukokotoa ucheleweshaji',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'activity-type' => 'Aina ya shughuli',
            'summary'       => 'Muhtasari',
            'assignment'    => 'Ukabidhiwaji',
            'assigned-to'   => 'Amekabidhiwa',
            'interval'      => 'Muda kati',
            'delay-unit'    => 'Kipimo cha ucheleweshaji',
            'delay-from'    => 'Ucheleweshaji kuanzia',
            'created-by'    => 'Imeundwa na',
            'created-at'    => 'Imeundwa',
            'updated-at'    => 'Imesasishwa',
        ],

        'groups' => [
            'activity-type' => 'Aina ya shughuli',
            'assignment'    => 'Ukabidhiwaji',
            'assigned-to'   => 'Amekabidhiwa',
            'interval'      => 'Muda kati',
            'delay-unit'    => 'Kipimo cha ucheleweshaji',
            'delay-from'    => 'Ucheleweshaji kuanzia',
            'created-by'    => 'Imeundwa na',
            'created-at'    => 'Imeundwa',
            'updated-at'    => 'Imesasishwa',
        ],

        'filters' => [
            'activity-type'   => 'Aina ya shughuli',
            'activity-status' => 'Hali ya shughuli',
            'has-delay'       => 'Ina ucheleweshaji',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Kiolezo cha shughuli kimesasishwa',
                    'body'  => 'Kiolezo cha shughuli kimesasishwa kikamilifu.',
                ],
            ],

            'create' => [
                'notification' => [
                    'title' => 'Kiolezo cha shughuli kimeundwa',
                    'body'  => 'Kiolezo cha shughuli kimeundwa kikamilifu.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Kiolezo cha shughuli kimefutwa',
                    'body'  => 'Kiolezo cha shughuli kimefutwa kikamilifu.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Violezo vya shughuli vimefutwa',
                    'body'  => 'Violezo vya shughuli vimefutwa kikamilifu.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'activity-details' => [
                'title' => 'Maelezo ya shughuli',

                'entries' => [
                    'activity-type' => 'Aina ya shughuli',
                    'summary'       => 'Muhtasari',
                ],
            ],

            'assignment' => [
                'title' => 'Ukabidhiwaji',

                'entries' => [
                    'assignment' => 'Ukabidhiwaji',
                    'assignee'   => 'Mkabidhiwa',
                ],
            ],

            'delay-information' => [
                'title' => 'Taarifa za ucheleweshaji',

                'entries' => [
                    'delay-count'            => 'Idadi ya ucheleweshaji',
                    'delay-unit'             => 'Kipimo cha ucheleweshaji',
                    'delay-from'             => 'Ucheleweshaji kuanzia',
                    'delay-from-helper-text' => 'Chanzo cha kukokotoa ucheleweshaji',
                ],
            ],
        ],

        'note' => 'Kidokezo',
    ],
];
