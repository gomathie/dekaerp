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

        'header-actions' => [
            'create' => [
                'notification' => [
                    'title' => 'Shughuli plan kiolezo imeundwa',
                    'body'  => 'shughuli plan kiolezo ina imeundwa successfully.',
                ],
            ],
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Shughuli kiolezo imesasishwa',
                    'body'  => 'shughuli kiolezo ina imesasishwa successfully.',
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
];
