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
                'title' => 'Ugawaji',

                'fields' => [
                    'assignment' => 'Ugawaji',
                    'assignee'   => 'Aliyekabidhiwa',
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
            'assignment'    => 'Ugawaji',
            'assigned-to'   => 'Amekabidhiwa',
            'interval'      => 'Muda',
            'delay-unit'    => 'Kipimo cha ucheleweshaji',
            'delay-from'    => 'Ucheleweshaji kuanzia',
            'created-by'    => 'Iliundwa na',
            'created-at'    => 'Iliundwa',
            'updated-at'    => 'Ilisasishwa',
        ],

        'groups' => [
            'activity-type' => 'Aina ya shughuli',
            'assignment'    => 'Ugawaji',
            'assigned-to'   => 'Amekabidhiwa',
            'interval'      => 'Muda',
            'delay-unit'    => 'Kipimo cha ucheleweshaji',
            'delay-from'    => 'Ucheleweshaji kuanzia',
            'created-by'    => 'Iliundwa na',
            'created-at'    => 'Iliundwa',
            'updated-at'    => 'Ilisasishwa',
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
                    'body'  => 'Kiolezo cha shughuli kimesasishwa kwa mafanikio.',
                ],
            ],

            'create' => [
                'notification' => [
                    'title' => 'Kiolezo cha shughuli kimeundwa',
                    'body'  => 'Kiolezo cha shughuli kimeundwa kwa mafanikio.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Kiolezo cha shughuli kimefutwa',
                    'body'  => 'Kiolezo cha shughuli kimefutwa kwa mafanikio.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Violezo vya shughuli vimefutwa',
                    'body'  => 'Violezo vya shughuli vimefutwa kwa mafanikio.',
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
                'title' => 'Ugawaji',

                'entries' => [
                    'assignment' => 'Ugawaji',
                    'assignee'   => 'Aliyekabidhiwa',
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
