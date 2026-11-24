<?php

return [
    'navigation' => [
        'title' => 'Категории',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Общие сведения',

                'fields' => [
                    'name'       => 'Название',
                    'technician' => 'Ответственный',
                    'company'    => 'Компания',
                    'note'       => 'Примечание',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'       => 'Название',
            'technician' => 'Ответственный',
            'company'    => 'Компания',
            'created-at' => 'Создано',
        ],

        'groups' => [
            'technician' => 'Ответственный',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Категория обновлена',
                    'body'  => 'Категория успешно обновлена.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Категория удалена',
                    'body'  => 'Категория успешно удалена.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Категории удалены',
                    'body'  => 'Категории успешно удалены.',
                ],
            ],
        ],

        'empty-state' => [
            'create' => [
                'notification' => [
                    'title' => 'Категория создана',
                    'body'  => 'Категория успешно создана.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Общие сведения',

                'entries' => [
                    'name'       => 'Название',
                    'technician' => 'Ответственный',
                    'company'    => 'Компания',
                    'note'       => 'Примечание',
                ],
            ],
        ],
    ],
];
