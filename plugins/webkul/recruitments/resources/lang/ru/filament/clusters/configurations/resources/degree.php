<?php

return [
    'title' => 'Degrees',

    'navigation' => [
        'title' => 'Degrees',
        'group' => 'Applications',
    ],

    'groups' => [
        'status'     => 'Статус',
        'created-by' => 'Создано От',
        'created-at' => 'Создано',
        'updated-at' => 'Обновлено',
    ],

    'form' => [
        'fields' => [
            'name'             => 'Название',
            'name-placeholder' => 'Введите название degree',
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
                    'title' => 'Degree обновлено',
                    'body'  => 'degree есть обновлено successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Degree удалено',
                    'body'  => 'degree есть удалено successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Degrees удалено',
                    'body'  => 'degrees есть удалено successfully.',
                ],
            ],
        ],

        'empty-state-action' => [
            'create' => [
                'notification' => [
                    'title' => 'Degree создано',
                    'body'  => 'degree есть создано successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'name' => 'Название',
    ],
];
