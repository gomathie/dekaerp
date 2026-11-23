<?php

return [
    'title' => 'Sources',

    'navigation' => [
        'title' => 'Sources',
        'group' => 'UTMs',
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
            'name-placeholder' => 'Введите название источник',
            'status'           => 'Статус',
        ],
    ],

    'table' => [
        'columns' => [
            'id'         => 'Ид.',
            'name'       => 'Название',
            'status'     => 'Статус',
            'created-by' => 'Создано От',
            'created-at' => 'Создано',
            'updated-at' => 'Обновлено',
        ],

        'filters' => [
            'name'       => 'Название',
            'created-by' => 'Создано От',
            'updated-at' => 'Обновлено',
            'created-at' => 'Создано',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Источник обновлено',
                    'body'  => 'источник есть обновлено successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Источник удалено',
                    'body'  => 'источник есть удалено successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Sources удалено',
                    'body'  => 'Sources есть удалено successfully.',
                ],
            ],
        ],

        'empty-state-action' => [
            'create' => [
                'notification' => [
                    'title' => 'Источник создано',
                    'body'  => 'источник есть создано successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'name' => 'Название',
    ],
];
