<?php

return [
    'navigation' => [
        'title' => 'Teams',
    ],

    'form' => [
        'name'    => 'Название',
        'company' => 'Компания',
        'users'   => 'Команда Участники',
    ],

    'table' => [
        'columns' => [
            'name'       => 'Название',
            'company'    => 'Компания',
            'users'      => 'Команда Участники',
            'created-at' => 'Создано',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Команда обновлено',
                    'body'  => 'команда есть обновлено successfully.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Команда restored',
                    'body'  => 'команда есть restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Команда удалено',
                    'body'  => 'команда есть удалено successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Команда force удалено',
                        'body'  => 'команда есть force удалено successfully.',
                    ],
                    'error' => [
                        'title' => 'Команда could не быть force удалено',
                        'body'  => 'команда используется и нельзя быть force удалено.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Teams restored',
                    'body'  => 'teams есть restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Teams удалено',
                    'body'  => 'teams есть удалено successfully.',
                ],
            ],
        ],
    ],
];
