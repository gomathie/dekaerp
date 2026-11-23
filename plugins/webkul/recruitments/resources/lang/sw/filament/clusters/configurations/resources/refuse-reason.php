<?php

return [
    'title' => 'Kataa Sababu',

    'navigation' => [
        'title' => 'Kataa Reasons',
        'group' => 'Applications',
    ],

    'form' => [
        'fields' => [
            'name'             => 'Jina',
            'template'         => [
                'title'                    => 'Kiolezo',
                'applicant-refuse'         => 'Mwombaji Kataa',
                'applicant-not-interested' => 'Mwombaji Si Interested',
            ],
            'name-placeholder' => 'Ingiza jina ya kataa sababu',
        ],
    ],

    'table' => [
        'columns' => [
            'id'         => 'Kitambulisho',
            'name'       => 'Jina',
            'template'   => 'Kiolezo',
            'created-by' => 'Imeundwa Na',
            'created-at' => 'Imeundwa',
            'updated-at' => 'Imesasishwa',
        ],

        'filters' => [
            'name'       => 'Jina',
            'employee'   => 'Mfanyakazi',
            'created-by' => 'Imeundwa Na',
            'updated-at' => 'Imesasishwa',
            'created-at' => 'Imeundwa',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Kataa sababu imesasishwa',
                    'body'  => 'kataa sababu ina imesasishwa successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Kataa sababu imefutwa',
                    'body'  => 'kataa sababu ina imefutwa successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Kataa reasons imefutwa',
                    'body'  => 'kataa reasons ina imefutwa successfully.',
                ],
            ],
        ],

        'empty-state-action' => [
            'create' => [
                'notification' => [
                    'title' => 'Kataa sababu imeundwa',
                    'body'  => 'kataa sababu ina imeundwa successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'name'       => 'Jina',
        'template'   => 'Kiolezo',
    ],
];
