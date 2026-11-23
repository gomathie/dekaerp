<?php

return [
    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Общее',

                'fields' => [
                    'name'             => 'Название',
                    'name-placeholder' => 'eg. Lamps',
                    'parent'           => 'Parent',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'        => 'Название',
            'full-name'   => 'Полный Название',
            'parent-path' => 'Parent Path',
            'parent'      => 'Parent',
            'creator'     => 'Creator',
            'created-at'  => 'Создано',
            'created-at'  => 'Создано',
            'updated-at'  => 'Обновлено',
        ],

        'groups' => [
            'parent'     => 'Parent',
            'creator'    => 'Creator',
            'created-at' => 'Создано',
            'updated-at' => 'Обновлено',
        ],

        'filters' => [
            'parent'  => 'Parent',
            'creator' => 'Creator',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Категория удалено',
                        'body'  => 'Категория есть удалено successfully.',
                    ],

                    'error' => [
                        'title' => 'Категория could не быть удалено',
                        'body'  => 'категория нельзя быть удалено потому что это currently в использовать.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Категории удалено',
                        'body'  => 'категории есть удалено successfully.',
                    ],

                    'error' => [
                        'title' => 'Категории could не быть удалено',
                        'body'  => 'категории нельзя быть удалено потому что они currently в использовать.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Общее Сведения',

                'entries' => [
                    'name'        => 'Название',
                    'parent'      => 'Parent Категория',
                    'full_name'   => 'Полный Категория Название',
                    'parent_path' => 'Категория Path',
                ],
            ],

            'record-information' => [
                'title' => 'Запись Сведения',

                'entries' => [
                    'creator'    => 'Создано От',
                    'created_at' => 'Создано',
                    'updated_at' => 'Последний Обновлено',
                ],
            ],
        ],
    ],
];
