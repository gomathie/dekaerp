<?php

return [
    'form' => [
        'sections' => [
            'fields' => [
                'title'        => 'Заголовок',
                'type'         => 'Тип',
                'name'         => 'Название',
                'type'         => 'Тип',
                'create-type'  => 'Создать тип',
                'duration'     => 'Период',
                'start-date'   => 'Дата начала',
                'end-date'     => 'Дата окончания',
                'display-type' => 'Тип отображения',
                'description'  => 'Описание',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'title'        => 'Заголовок',
            'start-date'   => 'Дата начала',
            'end-date'     => 'Дата окончания',
            'display-type' => 'Тип отображения',
            'description'  => 'Описание',
            'created-by'   => 'Создал',
            'created-at'   => 'Создано',
            'updated-at'   => 'Изменено',
        ],

        'groups' => [
            'group-by-type'         => 'Группировать по типу',
            'group-by-display-type' => 'Группировать по типу отображения',
        ],

        'header-actions' => [
            'add-resume' => 'Добавить запись резюме',
        ],

        'filters' => [
            'type'            => 'Тип',
            'start-date-from' => 'Дата начала с',
            'start-date-to'   => 'Дата начала по',
            'created-from'    => 'Создано с',
            'created-to'      => 'Создано по',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Запись резюме обновлена',
                    'body'  => 'Запись резюме обновлена.',
                ],
            ],

            'create' => [
                'notification' => [
                    'title' => 'Запись резюме создана',
                    'body'  => 'Запись резюме создана.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Запись резюме удалена',
                    'body'  => 'Запись резюме удалена.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Записи резюме удалены',
                    'body'  => 'Записи резюме удалены.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'title'        => 'Заголовок',
            'display-type' => 'Тип отображения',
            'type'         => 'Тип',
            'description'  => 'Описание',
            'duration'     => 'Период',
            'start-date'   => 'Дата начала',
            'end-date'     => 'Дата окончания',
        ],
    ],
];
