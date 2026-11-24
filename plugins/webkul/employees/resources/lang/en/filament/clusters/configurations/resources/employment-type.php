<?php

return [
    'title' => 'Employment Types',

    'navigation' => [
        'title' => 'Employment Types',
        'group' => 'Recruitment',
    ],

    'form' => [
        'fields' => [
            'name'    => 'Employment Type',
            'code'    => 'Code',
            'country' => 'Country',
        ],
    ],

    'table' => [
        'columns' => [
            'id'         => 'ID',
            'name'       => 'Employment Type',
            'code'       => 'Code',
            'country'    => 'Country',
            'created-by' => 'Created By',
            'created-at' => 'Created At',
            'updated-at' => 'Updated At',
        ],

        'filters' => [
            'name'       => 'Employment Type',
            'country'    => 'Country',
            'created-by' => 'Created By',
            'updated-at' => 'Updated At',
            'created-at' => 'Created At',
        ],

        'groups' => [
            'name'       => 'Employment Type',
            'country'    => 'Country',
            'code'       => 'Code',
            'created-by' => 'Created By',
            'created-at' => 'Created At',
            'updated-at' => 'Updated At',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Employment Type updated',
                    'body'  => 'The Employment Type has been edited successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Employment Type deleted',
                    'body'  => 'The Employment Type has been deleted successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Employment Types deleted',
                    'body'  => 'The employment types have been deleted successfully.',
                ],
            ],
        ],

        'empty-state-actions' => [
            'create' => [
                'notification' => [
                    'title' => 'Employment Type created',
                    'body'  => 'The employment type has been created successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'name'    => 'Employment Type',
            'code'    => 'Code',
            'country' => 'Country',
        ],
    ],
];
