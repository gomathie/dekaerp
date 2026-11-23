<?php

return [
    'navigation' => [
        'title' => 'Milestones',
    ],

    'form' => [
        'name'         => 'Jina',
        'deadline'     => 'Deadline',
        'is-completed' => 'Ni Imekamilika',
        'project'      => 'Mradi',
    ],

    'table' => [
        'columns' => [
            'name'         => 'Jina',
            'deadline'     => 'Deadline',
            'is-completed' => 'Ni Imekamilika',
            'completed-at' => 'Imekamilika Saa',
            'project'      => 'Mradi',
            'creator'      => 'Creator',
            'created-at'   => 'Imeundwa',
            'updated-at'   => 'Imesasishwa',
        ],

        'groups' => [
            'name'         => 'Jina',
            'is-completed' => 'Ni Imekamilika',
            'project'      => 'Mradi',
            'created-at'   => 'Imeundwa',
        ],

        'filters' => [
            'is-completed' => 'Ni Imekamilika',
            'project'      => 'Mradi',
            'creator'      => 'Creator',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Hatua kuu sasisha',
                    'body'  => 'hatua kuu ina sasisha successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Hatua kuu imefutwa',
                    'body'  => 'hatua kuu ina imefutwa successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Milestones imefutwa',
                    'body'  => 'milestones ina imefutwa successfully.',
                ],
            ],
        ],
    ],
];
