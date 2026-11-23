<?php

return [
    'form' => [
        'fields' => [
            'code' => 'Msimbo',
            'name' => 'Jina',
        ],
    ],

    'table' => [
        'columns' => [
            'code'       => 'Msimbo',
            'name'       => 'Jina',
            'created-by' => 'Imeundwa Na',
        ],

        'groups' => [
            'code' => 'Msimbo',
            'name' => 'Jina',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Incoterm imesasishwa',
                    'body'  => 'incoterm ina imesasishwa successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Incoterm imefutwa',
                    'body'  => 'incoterm ina imefutwa successfully.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Incoterm restored',
                    'body'  => 'incoterm ina restored successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Incoterms restored',
                    'body'  => 'incoterms ina restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Incoterms imefutwa',
                    'body'  => 'incoterms ina imefutwa successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Incoterms force imefutwa',
                    'body'  => 'incoterms ina force imefutwa successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'name' => 'Jina',
            'code' => 'Msimbo',
        ],
    ],
];
