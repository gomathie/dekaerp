<?php

return [
    'navigation' => [
        'title' => 'Этапы',
    ],

    'form' => [
        'fields' => [
            'name' => 'Название',
            'done' => 'Завершён',
        ],
    ],

    'table' => [
        'columns' => [
            'name'       => 'Название',
            'done'       => 'Завершён',
            'created-at' => 'Создано',
        ],

        'groups' => [
            'done'       => 'Завершён',
            'created-at' => 'Создано',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Этап обновлено',
                    'body'  => 'этап есть обновлено successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Этап удалено',
                    'body'  => 'этап есть удалено successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Этапы удалено',
                    'body'  => 'этапы есть удалено successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Общее Сведения',

                'entries' => [
                    'name' => 'Название',
                    'done' => 'Завершён',
                ],
            ],
        ],
    ],
];
