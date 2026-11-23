<?php

return [
    'navigation' => [
        'title' => 'Shughuli Plans',
    ],

    'form' => [
        'name'   => 'Jina',
        'status' => 'Hali',
    ],

    'table' => [
        'columns' => [
            'name'       => 'Jina',
            'status'     => 'Hali',
            'created-at' => 'Imeundwa',
            'updated-at' => 'Imesasishwa',
        ],

        'groups' => [
            'name'       => 'Jina',
            'status'     => 'Hali',
            'created-at' => 'Imeundwa',
            'updated-at' => 'Imesasishwa',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Shughuli Plan restored',
                    'body'  => 'shughuli plan ina restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Shughuli Plan imefutwa',
                    'body'  => 'shughuli plan ina imefutwa successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Shughuli Plan force imefutwa',
                    'body'  => 'shughuli plan ina force imefutwa successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Shughuli Plans restored',
                    'body'  => 'shughuli plans ina restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Shughuli Plans imefutwa',
                    'body'  => 'shughuli plans ina imefutwa successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Shughuli Plans force imefutwa',
                    'body'  => 'shughuli plans ina force imefutwa successfully.',
                ],
            ],
        ],

        'empty-state' => [
            'create' => [
                'notification' => [
                    'title' => 'Shughuli Plan imeundwa',
                    'body'  => 'shughuli plan ina imeundwa successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'name'   => 'Jina',
        'status' => 'Hali',
    ],
];
