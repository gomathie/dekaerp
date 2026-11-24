<?php

return [
    'title' => 'Обязательные дни',

    'model-label' => 'Обязательный день',

    'navigation' => [
        'title' => 'Обязательные дни',
    ],

    'form' => [
        'fields' => [
            'name'       => 'Название',
            'start-date' => 'Дата начала',
            'end-date'   => 'Дата окончания',
            'color'      => 'Цвет',
        ],
    ],

    'table' => [
        'columns' => [
            'name'         => 'Название',
            'company-name' => 'Компания',
            'created-by'   => 'Создал',
            'start-date'   => 'Дата начала',
            'end-date'     => 'Дата окончания',
        ],

        'filters' => [
            'name'         => 'Название',
            'company-name' => 'Компания',
            'created-by'   => 'Создал',
            'start-date'   => 'Дата начала',
            'end-date'     => 'Дата окончания',
            'created-at'   => 'Создано',
            'updated-at'   => 'Обновлено',
        ],

        'groups' => [
            'name'         => 'Название',
            'company-name' => 'Компания',
            'created-by'   => 'Создал',
            'start-date'   => 'Дата начала',
            'end-date'     => 'Дата окончания',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Обязательный день обновлён',
                    'body'  => 'Обязательный день успешно обновлён.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Обязательный день удалён',
                    'body'  => 'Обязательный день успешно удалён.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Обязательные дни удалены',
                    'body'  => 'Обязательные дни успешно удалены.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'name'       => 'Название',
            'start-date' => 'Дата начала',
            'end-date'   => 'Дата окончания',
            'color'      => 'Цвет',
        ],
    ],
];
