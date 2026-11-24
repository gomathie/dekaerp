<?php

return [
    'title' => 'Степени',

    'navigation' => [
        'title' => 'Степени',
        'group' => 'Заявки',
    ],

    'groups' => [
        'status'     => 'Статус',
        'created-by' => 'Создал',
        'created-at' => 'Дата создания',
        'updated-at' => 'Дата изменения',
    ],

    'form' => [
        'fields' => [
            'name'             => 'Название',
            'name-placeholder' => 'Введите название степени',
        ],
    ],

    'table' => [
        'columns' => [
            'id'         => 'ID',
            'name'       => 'Название',
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
                    'title' => 'Степень обновлена',
                    'body'  => 'Степень успешно обновлена.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Степень удалена',
                    'body'  => 'Степень успешно удалена.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Степени удалены',
                    'body'  => 'Степени успешно удалены.',
                ],
            ],
        ],

        'empty-state-action' => [
            'create' => [
                'notification' => [
                    'title' => 'Степень создана',
                    'body'  => 'Степень успешно создана.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'name' => 'Название',
    ],
];
