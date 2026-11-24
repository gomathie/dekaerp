<?php

return [
    'title' => 'Команды',

    'navigation' => [
        'title' => 'Команды',
    ],

    'form' => [
        'fields' => [
            'name' => 'Название',
        ],
    ],

    'table' => [
        'columns' => [
            'name'       => 'Название',
            'created-by' => 'Создал',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Команда обновлена',
                    'body'  => 'Команда успешно обновлена.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Команда удалена',
                    'body'  => 'Команда успешно удалена.',
                ],
            ],
        ],

        'empty-state-actions' => [
            'create' => [
                'notification' => [
                    'title' => 'Команды созданы',
                    'body'  => 'Команды успешно созданы.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'entries' => [
                'name'          => 'Название',
                'job-title'     => 'Должность',
                'work-email'    => 'Рабочая эл. почта',
                'work-mobile'   => 'Рабочий мобильный',
                'work-phone'    => 'Рабочий телефон',
                'manager'       => 'Руководитель',
                'department'    => 'Отдел',
                'job-position'  => 'Позиция',
                'team-tags'     => 'Теги команды',
                'coach'         => 'Наставник',
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'name' => 'Название',
        ],
    ],
];
