<?php

return [
    'title' => 'Источники',

    'navigation' => [
        'title' => 'Источники',
        'group' => 'UTM',
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
            'name-placeholder' => 'Введите название источника',
            'status'           => 'Статус',
        ],
    ],

    'table' => [
        'columns' => [
            'id'         => 'ID',
            'name'       => 'Название',
            'status'     => 'Статус',
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
                    'title' => 'Источник обновлён',
                    'body'  => 'Источник успешно обновлён.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Источник удалён',
                    'body'  => 'Источник успешно удалён.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Источники удалены',
                    'body'  => 'Источники успешно удалены.',
                ],
            ],
        ],

        'empty-state-action' => [
            'create' => [
                'notification' => [
                    'title' => 'Источник создан',
                    'body'  => 'Источник успешно создан.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'name' => 'Название',
    ],
];
