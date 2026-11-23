<?php

return [
    'title' => 'Departure Reasons',

    'navigation' => [
        'title' => 'Departure Reasons',
        'group' => 'Сотрудник',
    ],

    'groups' => [
        'status'     => 'Статус',
        'created-by' => 'Создано От',
        'created-at' => 'Создано',
        'updated-at' => 'Обновлено',
    ],

    'form' => [
        'fields' => [
            'name' => 'Название',
        ],
    ],

    'table' => [
        'columns' => [
            'id'         => 'Ид.',
            'name'       => 'Название',
            'created-by' => 'Создано От',
            'created-at' => 'Создано',
            'updated-at' => 'Обновлено',
        ],

        'filters' => [
            'name'       => 'Название',
            'employee'   => 'Сотрудник',
            'created-by' => 'Создано От',
            'updated-at' => 'Обновлено',
            'created-at' => 'Создано',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Departure причина обновлено',
                    'body'  => 'departure причина есть обновлено successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Departure причина удалено',
                    'body'  => 'departure причина есть удалено successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Departure reasons удалено',
                    'body'  => 'departure reasons есть удалено successfully.',
                ],
            ],
        ],

        'empty-state-action' => [
            'create' => [
                'notification' => [
                    'title' => 'Departure причина создано',
                    'body'  => 'departure причина есть создано successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'name' => 'Название',
    ],
];
