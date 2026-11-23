<?php

return [
    'title' => 'Yangu Allocation',

    'model-label' => 'Yangu Allocation',

    'navigation' => [
        'title' => 'Yangu Allocation',
    ],

    'form' => [
        'fields' => [
            'name'                => 'Jina',
            'name-placeholder'    => 'Likizo Aina (Kutoka validity anza hadi validity mwisho/hakuna limit)',
            'time-off-type'       => 'Likizo Aina',
            'allocation-type'     => 'Allocation Aina',
            'validity-period'     => 'Validity Kipindi',
            'date-from'           => 'Tarehe Kutoka',
            'date-to'             => 'Tarehe Hadi',
            'date-to-placeholder' => 'Hakuna Limit',
            'allocation'          => 'Allocation',
            'allocation-suffix'   => 'Nambari ya Siku',
            'reason'              => 'Sababu',
        ],
    ],

    'table' => [
        'columns' => [
            'time-off-type'   => 'Likizo Aina',
            'amount'          => 'Kiasi',
            'allocation-type' => 'Allocation Aina',
            'status'          => 'Hali',
        ],

        'groups' => [
            'time-off-type'   => 'Likizo Aina',
            'employee-name'   => 'Mfanyakazi Jina',
            'allocation-type' => 'Allocation Aina',
            'status'          => 'Hali',
            'start-date'      => 'Anza Tarehe',
        ],

        'actions' => [
            'approve' => [
                'title' => [
                    'validate' => 'Validate',
                    'approve'  => 'Idhinisha',
                ],
                'notification' => [
                    'title' => 'Allocation imeidhinishwa imeidhinishwa',
                    'body'  => 'allocation imeidhinishwa ina imeidhinishwa successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Allocation imefutwa',
                    'body'  => 'allocation ina imefutwa successfully.',
                ],
            ],

            'refused' => [
                'title'        => 'Kataa',
                'notification' => [
                    'title' => 'Allocation imekataliwa',
                    'body'  => 'allocation ina imekataliwa successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Allocations imefutwa',
                    'body'  => 'allocations ina imefutwa successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'allocation-details' => [
                'title'   => 'Allocation Maelezo',
                'entries' => [
                    'name'                => 'Jina',
                    'time-off-type'       => 'Likizo Aina',
                    'allocation-type'     => 'Allocation Aina',
                ],
            ],

            'validity-period' => [
                'title'   => 'Validity Kipindi',
                'entries' => [
                    'date-from' => 'Tarehe Kutoka',
                    'date-to'   => 'Tarehe Hadi',
                    'reason'    => 'Sababu',
                ],
            ],
            'allocation-status' => [
                'title'   => 'Allocation Hali',
                'entries' => [
                    'date-to-placeholder' => 'Hakuna Limit',
                    'allocation'          => 'Nambari ya Siku(s)',
                    'allocation-value'    => ':days nambari ya siku',
                    'state'               => 'Hali',
                ],
            ],
        ],
    ],
];
