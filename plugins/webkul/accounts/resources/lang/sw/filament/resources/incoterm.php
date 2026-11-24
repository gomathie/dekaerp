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
            'created-by' => 'Imeundwa na',
        ],

        'groups' => [
            'code' => 'Msimbo',
            'name' => 'Jina',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Incoterm imesasishwa',
                    'body'  => 'Incoterm imesasishwa kikamilifu.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Incoterm imefutwa',
                    'body'  => 'Incoterm imefutwa kikamilifu.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Incoterm imerejeshwa',
                    'body'  => 'Incoterm imerejeshwa kikamilifu.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Incoterm zimerejeshwa',
                    'body'  => 'Incoterm zimerejeshwa kikamilifu.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Incoterm zimefutwa',
                    'body'  => 'Incoterm zimefutwa kikamilifu.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Incoterm zimefutwa kabisa',
                    'body'  => 'Incoterm zimefutwa kabisa kikamilifu.',
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
