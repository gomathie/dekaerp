<?php

return [
    'title' => 'Причины увольнения',

    'navigation' => [
        'title' => 'Причины увольнения',
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
            'name' => 'Название',
        ],
    ],

    'table' => [
        'columns' => [
            'id'         => 'ID',
            'name'       => 'Название',
            'created-by' => 'Создал',
            'created-at' => 'Создано',
            'updated-at' => 'Изменено',
        ],

        'filters' => [
            'name'       => 'Название',
            'employee'   => 'Сотрудник',
            'created-by' => 'Создал',
            'updated-at' => 'Изменено',
            'created-at' => 'Создано',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Причина увольнения обновлена',
                    'body'  => 'Причина увольнения обновлена.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Причина увольнения удалена',
                    'body'  => 'Причина увольнения удалена.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Причины увольнения удалены',
                    'body'  => 'Причины увольнения удалены.',
                ],
            ],
        ],

        'empty-state-action' => [
            'create' => [
                'notification' => [
                    'title' => 'Причина увольнения создана',
                    'body'  => 'Причина увольнения создана.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'name' => 'Название',
    ],
];
