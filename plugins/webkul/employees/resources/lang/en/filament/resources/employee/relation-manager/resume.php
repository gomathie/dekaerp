<?php

return [
    'form' => [
        'sections' => [
            'fields' => [
                'title'        => 'Title',
                'type'         => 'Type',
                'name'         => 'Name',
                'type'         => 'Type',
                'create-type'  => 'Create Type',
                'duration'     => 'Duration',
                'start-date'   => 'Start Date',
                'end-date'     => 'End Date',
                'display-type' => 'Display Type',
                'description'  => 'Description',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'title'        => 'Title',
            'start-date'   => 'Start Date',
            'end-date'     => 'End Date',
            'display-type' => 'Display Type',
            'description'  => 'Description',
            'created-by'   => 'Created By',
            'created-at'   => 'Created At',
            'updated-at'   => 'Updated At',
        ],

        'groups' => [
            'group-by-type'         => 'Group By Type',
            'group-by-display-type' => 'Group By Display Type',
        ],

        'header-actions' => [
            'add-resume' => 'Add Resume',
        ],

        'filters' => [
            'type'            => 'Type',
            'start-date-from' => 'Start Date From',
            'start-date-to'   => 'Start Date To',
            'created-from'    => 'Created From',
            'created-to'      => 'Created To',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Resume entry updated',
                    'body'  => 'The resume entry has been updated successfully.',
                ],
            ],

            'create' => [
                'notification' => [
                    'title' => 'Resume entry created',
                    'body'  => 'The resume entry has been created successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Resume entry deleted',
                    'body'  => 'The resume entry has been deleted successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Resume entries deleted',
                    'body'  => 'The resume entries have been deleted successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'title'        => 'Title',
            'display-type' => 'Display Type',
            'type'         => 'Type',
            'description'  => 'Description',
            'duration'     => 'Duration',
            'start-date'   => 'Start Date',
            'end-date'     => 'End Date',
        ],
    ],
];
