<?php

return [
    'title' => 'Каналы',

    'navigation' => [
        'title' => 'Каналы',
        'group' => 'UTM',
    ],

    'form' => [
        'fields' => [
            'name'             => 'Название',
            'name-placeholder' => 'Введите название канала',
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
            'created-by' => 'Создал',
            'updated-at' => 'Дата изменения',
            'created-at' => 'Дата создания',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Канал обновлён',
                    'body'  => 'Канал успешно обновлён.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Канал удалён',
                    'body'  => 'Канал успешно удалён.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Каналы удалены',
                    'body'  => 'Каналы успешно удалены.',
                ],
            ],
        ],

        'empty-state-action' => [
            'create' => [
                'notification' => [
                    'title' => 'Канал создан',
                    'body'  => 'Канал успешно создан.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'name' => 'Название',
    ],
];
