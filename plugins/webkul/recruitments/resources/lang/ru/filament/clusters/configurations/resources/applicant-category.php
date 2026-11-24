<?php

return [
    'title' => 'Теги',

    'navigation' => [
        'title' => 'Теги',
        'group' => 'Заявки',
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
            'created-at' => 'Дата создания',
            'updated-at' => 'Дата изменения',
        ],

        'filters' => [
            'name'       => 'Название',
            'employee'   => 'Сотрудник',
            'created-by' => 'Создал',
            'updated-at' => 'Дата изменения',
            'created-at' => 'Дата создания',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Теги обновлены',
                    'body'  => 'Теги успешно обновлены.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Теги удалены',
                    'body'  => 'Теги успешно удалены.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Категории соискателей удалены',
                    'body'  => 'Категории соискателей успешно удалены.',
                ],
            ],
        ],

        'empty-state-action' => [
            'create' => [
                'notification' => [
                    'title' => 'Теги созданы',
                    'body'  => 'Теги успешно созданы.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'name'  => 'Название',
        'color' => 'Цвет',
    ],
];
