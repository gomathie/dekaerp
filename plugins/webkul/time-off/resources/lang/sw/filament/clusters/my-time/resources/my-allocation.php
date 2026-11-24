<?php

return [
    'title' => 'Mgao wangu',

    'model-label' => 'Mgao wangu',

    'navigation' => [
        'title' => 'Mgao wangu',
    ],

    'form' => [
        'fields' => [
            'name'                => 'Jina',
            'name-placeholder'    => 'Aina ya likizo (kuanzia mwanzo hadi mwisho wa uhalali/bila kikomo)',
            'time-off-type'       => 'Aina ya likizo',
            'allocation-type'     => 'Aina ya mgao',
            'validity-period'     => 'Kipindi cha uhalali',
            'date-from'           => 'Tarehe kuanzia',
            'date-to'             => 'Tarehe hadi',
            'date-to-placeholder' => 'Bila kikomo',
            'allocation'          => 'Mgao',
            'allocation-suffix'   => 'Idadi ya siku',
            'reason'              => 'Sababu',
        ],
    ],

    'table' => [
        'columns' => [
            'time-off-type'   => 'Aina ya likizo',
            'amount'          => 'Kiasi',
            'allocation-type' => 'Aina ya mgao',
            'status'          => 'Hali',
        ],

        'groups' => [
            'time-off-type'   => 'Aina ya likizo',
            'employee-name'   => 'Jina la mfanyakazi',
            'allocation-type' => 'Aina ya mgao',
            'status'          => 'Hali',
            'start-date'      => 'Tarehe ya kuanza',
        ],

        'actions' => [
            'approve' => [
                'title' => [
                    'validate' => 'Thibitisha',
                    'approve'  => 'Idhinisha',
                ],
                'notification' => [
                    'title' => 'Mgao umeidhinishwa',
                    'body'  => 'Mgao umeidhinishwa kwa mafanikio.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Mgao umefutwa',
                    'body'  => 'Mgao umefutwa kwa mafanikio.',
                ],
            ],

            'refused' => [
                'title'        => 'Kataa',
                'notification' => [
                    'title' => 'Mgao umekataliwa',
                    'body'  => 'Mgao umekataliwa kwa mafanikio.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Migao imefutwa',
                    'body'  => 'Migao imefutwa kwa mafanikio.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'allocation-details' => [
                'title'   => 'Maelezo ya mgao',
                'entries' => [
                    'name'                => 'Jina',
                    'time-off-type'       => 'Aina ya likizo',
                    'allocation-type'     => 'Aina ya mgao',
                ],
            ],

            'validity-period' => [
                'title'   => 'Kipindi cha uhalali',
                'entries' => [
                    'date-from' => 'Tarehe kuanzia',
                    'date-to'   => 'Tarehe hadi',
                    'reason'    => 'Sababu',
                ],
            ],
            'allocation-status' => [
                'title'   => 'Hali ya mgao',
                'entries' => [
                    'date-to-placeholder' => 'Bila kikomo',
                    'allocation'          => 'Idadi ya siku',
                    'allocation-value'    => 'Siku :days',
                    'state'               => 'Hali',
                ],
            ],
        ],
    ],
];
