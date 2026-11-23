<?php

return [
    'form' => [
        'name' => 'Jina',
    ],

    'table' => [
        'columns' => [
            'name'       => 'Jina',
            'created-at' => 'Imeundwa',
            'updated-at' => 'Imesasishwa',
        ],

        'groups' => [
            'created-at' => 'Imeundwa',
        ],

        'filters' => [
            'deleted-records' => 'Imefutwa Rekodi',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Skill imesasishwa',
                    'body'  => 'skill ina imesasishwa successfully.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Skill restored',
                    'body'  => 'skill ina restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Skill imefutwa',
                    'body'  => 'skill ina imefutwa successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Skills imefutwa',
                    'body'  => 'skills ina imefutwa successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Skills force imefutwa',
                    'body'  => 'skills ina force imefutwa successfully.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Skills force restored',
                    'body'  => 'skills ina force restored successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'name' => 'Jina',
        ],
    ],
];
