<?php

return [
    'form' => [
        'name'       => 'Название',
        'short-name' => 'Сокращение',
    ],

    'table' => [
        'columns' => [
            'name'       => 'Название',
            'short-name' => 'Сокращение',
            'created-at' => 'Создано',
            'updated-at' => 'Обновлено',
        ],

        'filters' => [
            'creator' => 'Создатель',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Обращение обновлено',
                    'body'  => 'Обращение успешно обновлено.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Обращение удалено',
                    'body'  => 'Обращение успешно удалено.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Обращения удалены',
                    'body'  => 'Обращения успешно удалены.',
                ],
            ],
        ],
    ],
];
