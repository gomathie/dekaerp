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
                    'delay-from-helper-text' => 'Msingi wa kukokotoa ucheleweshaji',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'activity-type' => 'Aina ya shughuli',
            'summary'       => 'Muhtasari',
            'assignment'    => 'Ugawaji',
            'assigned-to'   => 'Amepewa',
            'interval'      => 'Muda',
            'delay-unit'    => 'Kipimo cha ucheleweshaji',
            'delay-from'    => 'Ucheleweshaji kuanzia',
            'created-by'    => 'Imeundwa na',
            'created-at'    => 'Tarehe ya kuundwa',
            'updated-at'    => 'Tarehe ya kusasishwa',
        ],

        'groups' => [
            'activity-type' => 'Aina ya shughuli',
            'assignment'    => 'Ugawaji',
            'assigned-to'   => 'Amepewa',
            'interval'      => 'Muda',
            'delay-unit'    => 'Kipimo cha ucheleweshaji',
            'delay-from'    => 'Ucheleweshaji kuanzia',
            'created-by'    => 'Imeundwa na',
            'created-at'    => 'Tarehe ya kuundwa',
            'updated-at'    => 'Tarehe ya kusasishwa',
        ],

        'filters' => [
            'activity-type'   => 'Aina ya shughuli',
            'activity-status' => 'Hali ya shughuli',
            'has-delay'       => 'Ina ucheleweshaji',
        ],

        'header-actions' => [
            'create' => [
                'notification' => [
                    'title' => 'Kiolezo cha mpango wa shughuli kimeundwa',
                    'body'  => 'Kiolezo cha mpango wa shughuli kimeundwa kwa mafanikio.',
                ],
            ],
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Kiolezo cha shughuli kimesasishwa',
                    'body'  => 'Kiolezo cha shughuli kimesasishwa kwa mafanikio.',
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
];
