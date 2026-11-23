<?php

return [
    'title' => 'Teams',

    'navigation' => [
        'title' => 'Teams',
    ],

    'form' => [
        'fields' => [
            'name' => 'Название',
        ],
    ],

    'table' => [
        'columns' => [
            'name'       => 'Название',
            'created-by' => 'Создано От',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Команда обновлено',
                    'body'  => 'команда есть обновлено successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Команда удалено',
                    'body'  => 'команда есть удалено successfully.',
                ],
            ],
        ],

        'empty-state-actions' => [
            'create' => [
                'notification' => [
                    'title' => 'Teams создано',
                    'body'  => 'teams есть создано successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'entries' => [
                'name'          => 'Название',
                'job-title'     => 'Должность Заголовок',
                'work-email'    => 'Work Эл. почта',
                'work-mobile'   => 'Work Mobile',
                'work-phone'    => 'Work Телефон',
                'manager'       => 'Руководитель',
                'department'    => 'Отдел',
                'job-position'  => 'Должность Должность',
                'team-tags'     => 'Команда Метки',
                'coach'         => 'Coach',
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'name' => 'Название',
        ],
    ],
];
