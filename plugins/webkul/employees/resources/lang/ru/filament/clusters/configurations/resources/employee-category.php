<?php

return [
    'title' => 'Метки',

    'navigation' => [
        'title' => 'Метки',
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
            'name'             => 'Название',
            'name-placeholder' => 'Введите название метка',
            'color'            => 'Цвет',
        ],
    ],

    'table' => [
        'columns' => [
            'id'         => 'Ид.',
            'name'       => 'Название',
            'color'      => 'Цвет',
            'created-by' => 'Создано От',
            'created-at' => 'Создано',
            'updated-at' => 'Обновлено',
        ],

        'filters' => [
            'name'       => 'Название',
            'created-by' => 'Создано От',
            'updated-by' => 'Обновлено От',
            'updated-at' => 'Обновлено',
            'created-at' => 'Создано',
        ],

        'groups' => [
            'name'         => 'Название',
            'job-position' => 'Должность Должность',
            'color'        => 'Цвет',
            'created-by'   => 'Создано От',
            'created-at'   => 'Создано',
            'updated-at'   => 'Обновлено',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Метка обновлено',
                    'body'  => 'метка есть обновлено successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Метка удалено',
                    'body'  => 'метка есть удалено successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Метки удалено',
                    'body'  => 'метки есть удалено successfully.',
                ],
            ],
        ],

        'empty-state-action' => [
            'create' => [
                'notification' => [
                    'title' => 'Метка создано',
                    'body'  => 'метка есть создано successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'name'  => 'Название',
        'color' => 'Цвет',
    ],
];
