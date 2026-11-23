<?php

return [
    'form' => [
        'name'       => 'Название',
        'short-name' => 'Short Название',
    ],

    'table' => [
        'columns' => [
            'name'       => 'Название',
            'short-name' => 'Short Название',
            'created-at' => 'Создано',
            'updated-at' => 'Обновлено',
        ],

        'filters' => [
            'creator' => 'Creator',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Заголовок обновлено',
                    'body'  => 'Заголовок есть обновлено successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Заголовок удалено',
                    'body'  => 'заголовок есть удалено successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Titles удалено',
                    'body'  => 'titles есть удалено successfully.',
                ],
            ],
        ],
    ],
];
