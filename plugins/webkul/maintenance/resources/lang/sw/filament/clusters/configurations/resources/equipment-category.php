<?php

return [
    'navigation' => [
        'title' => 'Kategoria',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Jumla Taarifa',

                'fields' => [
                    'name'       => 'Jina',
                    'technician' => 'Responsible',
                    'company'    => 'Kampuni',
                    'note'       => 'Maelezo',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'       => 'Jina',
            'technician' => 'Responsible',
            'company'    => 'Kampuni',
            'created-at' => 'Imeundwa',
        ],

        'groups' => [
            'technician' => 'Responsible',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Kategoria imesasishwa',
                    'body'  => 'kategoria ina imesasishwa successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Kategoria imefutwa',
                    'body'  => 'kategoria ina imefutwa successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Kategoria imefutwa',
                    'body'  => 'kategoria ina imefutwa successfully.',
                ],
            ],
        ],

        'empty-state' => [
            'create' => [
                'notification' => [
                    'title' => 'Kategoria imeundwa',
                    'body'  => 'kategoria ina imeundwa successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Jumla Taarifa',

                'entries' => [
                    'name'       => 'Jina',
                    'technician' => 'Responsible',
                    'company'    => 'Kampuni',
                    'note'       => 'Maelezo',
                ],
            ],
        ],
    ],
];
