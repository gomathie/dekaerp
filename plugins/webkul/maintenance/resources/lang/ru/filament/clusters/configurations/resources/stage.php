<?php

return [
    'navigation' => [
        'title' => 'Этапы',
    ],

    'form' => [
        'fields' => [
            'name' => 'Название',
            'done' => 'Выполнено',
        ],
    ],

    'table' => [
        'columns' => [
            'name'       => 'Название',
            'done'       => 'Выполнено',
            'created-at' => 'Создано',
        ],

        'groups' => [
            'done'       => 'Выполнено',
            'created-at' => 'Создано',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Этап обновлён',
                    'body'  => 'Этап успешно обновлён.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Этап удалён',
                    'body'  => 'Этап успешно удалён.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Этапы удалены',
                    'body'  => 'Этапы успешно удалены.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Общие сведения',

                'entries' => [
                    'name' => 'Название',
                    'done' => 'Выполнено',
                ],
            ],
        ],
    ],
];
