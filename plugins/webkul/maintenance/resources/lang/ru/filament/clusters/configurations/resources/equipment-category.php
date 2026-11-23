<?php

return [
    'navigation' => [
        'title' => 'Категории',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Общее Сведения',

                'fields' => [
                    'name'       => 'Название',
                    'technician' => 'Responsible',
                    'company'    => 'Компания',
                    'note'       => 'Заметка',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'       => 'Название',
            'technician' => 'Responsible',
            'company'    => 'Компания',
            'created-at' => 'Создано',
        ],

        'groups' => [
            'technician' => 'Responsible',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Категория обновлено',
                    'body'  => 'категория есть обновлено successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Категория удалено',
                    'body'  => 'категория есть удалено successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Категории удалено',
                    'body'  => 'категории есть удалено successfully.',
                ],
            ],
        ],

        'empty-state' => [
            'create' => [
                'notification' => [
                    'title' => 'Категория создано',
                    'body'  => 'категория есть создано successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Общее Сведения',

                'entries' => [
                    'name'       => 'Название',
                    'technician' => 'Responsible',
                    'company'    => 'Компания',
                    'note'       => 'Заметка',
                ],
            ],
        ],
    ],
];
