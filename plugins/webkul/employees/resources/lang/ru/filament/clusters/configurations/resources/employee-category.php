<?php

return [
    'title' => 'Теги',

    'navigation' => [
        'title' => 'Теги',
        'group' => 'Сотрудник',
    ],

    'groups' => [
        'status'     => 'Статус',
        'created-by' => 'Создал',
        'created-at' => 'Создано',
        'updated-at' => 'Изменено',
    ],

    'form' => [
        'fields' => [
            'name'             => 'Название',
            'name-placeholder' => 'Введите название тега',
            'color'            => 'Цвет',
        ],
    ],

    'table' => [
        'columns' => [
            'id'         => 'ID',
            'name'       => 'Название',
            'color'      => 'Цвет',
            'created-by' => 'Создал',
            'created-at' => 'Создано',
            'updated-at' => 'Изменено',
        ],

        'filters' => [
            'name'       => 'Название',
            'created-by' => 'Создал',
            'updated-by' => 'Изменил',
            'updated-at' => 'Изменено',
            'created-at' => 'Создано',
        ],

        'groups' => [
            'name'         => 'Название',
            'job-position' => 'Должность',
            'color'        => 'Цвет',
            'created-by'   => 'Создал',
            'created-at'   => 'Создано',
            'updated-at'   => 'Изменено',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Тег обновлён',
                    'body'  => 'Тег обновлён.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Тег удалён',
                    'body'  => 'Тег удалён.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Теги удалены',
                    'body'  => 'Теги удалены.',
                ],
            ],
        ],

        'empty-state-action' => [
            'create' => [
                'notification' => [
                    'title' => 'Тег создан',
                    'body'  => 'Тег создан.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'name'  => 'Название',
        'color' => 'Цвет',
    ],
];
