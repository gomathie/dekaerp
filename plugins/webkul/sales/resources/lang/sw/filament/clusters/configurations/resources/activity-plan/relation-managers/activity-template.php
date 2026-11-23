<?php

return [
    'form' => [
        'sections' => [
            'activity-details' => [
                'title' => 'Shughuli Maelezo',

                'fields' => [
                    'activity-type' => 'Shughuli Aina',
                    'summary'       => 'Muhtasari',
                    'note'          => 'Maelezo',
                ],
            ],

            'assignment' => [
                'title' => 'Assignment',

                'fields' => [
                    'assignment' => 'Assignment',
                    'assignee'   => 'Assignee',
                ],
            ],

            'delay-information' => [
                'title' => 'Delay Taarifa',

                'fields' => [
                    'delay-count'            => 'Delay Idadi',
                    'delay-unit'             => 'Delay Uniti',
                    'delay-from'             => 'Delay Kutoka',
                    'delay-from-helper-text' => 'Chanzo ya delay calculation',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'activity-type' => 'Shughuli Aina',
            'summary'       => 'Muhtasari',
            'assignment'    => 'Assignment',
            'assigned-to'   => 'Amekabidhiwa Hadi',
            'interval'      => 'Interval',
            'delay-unit'    => 'Delay Uniti',
            'delay-from'    => 'Delay Kutoka',
            'created-by'    => 'Imeundwa Na',
            'created-at'    => 'Imeundwa',
            'updated-at'    => 'Imesasishwa',
        ],

        'groups' => [
            'activity-type' => 'Shughuli Aina',
            'assignment'    => 'Assignment',
            'assigned-to'   => 'Amekabidhiwa Hadi',
            'interval'      => 'Interval',
            'delay-unit'    => 'Delay Uniti',
            'delay-from'    => 'Delay Kutoka',
            'created-by'    => 'Imeundwa Na',
            'created-at'    => 'Imeundwa',
            'updated-at'    => 'Imesasishwa',
        ],

        'filters' => [
            'activity-type'   => 'Shughuli Aina',
            'activity-status' => 'Shughuli Hali',
            'has-delay'       => 'Ina Delay',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Shughuli kiolezo imesasishwa',
                    'body'  => 'shughuli kiolezo ina imesasishwa successfully.',
                ],
            ],

            'create' => [
                'notification' => [
                    'title' => 'Shughuli kiolezo imeundwa',
                    'body'  => 'shughuli kiolezo ina imeundwa successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Shughuli kiolezo imefutwa',
                    'body'  => 'shughuli kiolezo ina imefutwa successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Shughuli violezo imefutwa',
                    'body'  => 'shughuli violezo ina imefutwa successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'activity-details' => [
                'title' => 'Shughuli Maelezo',

                'entries' => [
                    'activity-type' => 'Shughuli Aina',
                    'summary'       => 'Muhtasari',
                ],
            ],

            'assignment' => [
                'title' => 'Assignment',

                'entries' => [
                    'assignment' => 'Assignment',
                    'assignee'   => 'Assignee',
                ],
            ],

            'delay-information' => [
                'title' => 'Delay Taarifa',

                'entries' => [
                    'delay-count'            => 'Delay Idadi',
                    'delay-unit'             => 'Delay Uniti',
                    'delay-from'             => 'Delay Kutoka',
                    'delay-from-helper-text' => 'Chanzo ya delay calculation',
                ],
            ],
        ],

        'note' => 'Maelezo',
    ],
];
