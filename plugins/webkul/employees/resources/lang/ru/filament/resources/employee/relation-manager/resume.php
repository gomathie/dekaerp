<?php

return [
    'form' => [
        'sections' => [
            'fields' => [
                'title'        => 'Заголовок',
                'type'         => 'Тип',
                'name'         => 'Название',
                'type'         => 'Тип',
                'create-type'  => 'Создать Тип',
                'duration'     => 'Duration',
                'start-date'   => 'Начать Дата',
                'end-date'     => 'Конец Дата',
                'display-type' => 'Display Тип',
                'description'  => 'Описание',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'title'        => 'Заголовок',
            'start-date'   => 'Начать Дата',
            'end-date'     => 'Конец Дата',
            'display-type' => 'Display Тип',
            'description'  => 'Описание',
            'created-by'   => 'Создано От',
            'created-at'   => 'Создано',
            'updated-at'   => 'Обновлено',
        ],

        'groups' => [
            'group-by-type'         => 'Группа От Тип',
            'group-by-display-type' => 'Группа От Display Тип',
        ],

        'header-actions' => [
            'add-resume' => 'Добавить Resume',
        ],

        'filters' => [
            'type'            => 'Тип',
            'start-date-from' => 'Начать Дата От',
            'start-date-to'   => 'Начать Дата До',
            'created-from'    => 'Создано От',
            'created-to'      => 'Создано До',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Skill Level обновлено',
                    'body'  => 'skill level есть обновлено successfully.',
                ],
            ],

            'create' => [
                'notification' => [
                    'title' => 'Skill Level создано',
                    'body'  => 'skill level есть создано successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Skill Level удалено',
                    'body'  => 'skill level есть удалено successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Skills удалено',
                    'body'  => 'skills есть удалено successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'title'        => 'Заголовок',
            'display-type' => 'Display Тип',
            'type'         => 'Тип',
            'description'  => 'Описание',
            'duration'     => 'Duration',
            'start-date'   => 'Начать Дата',
            'end-date'     => 'Конец Дата',
        ],
    ],
];
