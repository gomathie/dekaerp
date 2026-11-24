<?php

return [
    'form' => [
        'sections' => [
            'activity-details' => [
                'title' => 'Сведения о действии',

                'fields' => [
                    'activity-type' => 'Тип действия',
                    'summary'       => 'Сводка',
                    'note'          => 'Примечание',
                ],
            ],

            'assignment' => [
                'title' => 'Назначение',

                'fields' => [
                    'assignment' => 'Назначение',
                    'assignee'   => 'Исполнитель',
                ],
            ],

            'delay-information' => [
                'title' => 'Сведения об отсрочке',

                'fields' => [
                    'delay-count'            => 'Величина отсрочки',
                    'delay-unit'             => 'Единица отсрочки',
                    'delay-from'             => 'Отсрочка от',
                    'delay-from-helper-text' => 'Начало отсчёта отсрочки',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'activity-type' => 'Тип действия',
            'summary'       => 'Сводка',
            'assignment'    => 'Назначение',
            'assigned-to'   => 'Ответственный',
            'interval'      => 'Интервал',
            'delay-unit'    => 'Единица отсрочки',
            'delay-from'    => 'Отсрочка от',
            'created-by'    => 'Создал',
            'created-at'    => 'Создано',
            'updated-at'    => 'Изменено',
        ],

        'groups' => [
            'activity-type' => 'Тип действия',
            'assignment'    => 'Назначение',
            'assigned-to'   => 'Ответственный',
            'interval'      => 'Интервал',
            'delay-unit'    => 'Единица отсрочки',
            'delay-from'    => 'Отсрочка от',
            'created-by'    => 'Создал',
            'created-at'    => 'Создано',
            'updated-at'    => 'Изменено',
        ],

        'filters' => [
            'activity-type'   => 'Тип действия',
            'activity-status' => 'Статус действия',
            'has-delay'       => 'С отсрочкой',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Шаблон действия обновлён',
                    'body'  => 'Шаблон действия обновлён.',
                ],
            ],

            'create' => [
                'notification' => [
                    'title' => 'Шаблон действия создан',
                    'body'  => 'Шаблон действия создан.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Шаблон действия удалён',
                    'body'  => 'Шаблон действия удалён.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Шаблоны действий удалены',
                    'body'  => 'Шаблоны действий удалены.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'activity-details' => [
                'title' => 'Сведения о действии',

                'entries' => [
                    'activity-type' => 'Тип действия',
                    'summary'       => 'Сводка',
                ],
            ],

            'assignment' => [
                'title' => 'Назначение',

                'entries' => [
                    'assignment' => 'Назначение',
                    'assignee'   => 'Исполнитель',
                ],
            ],

            'delay-information' => [
                'title' => 'Сведения об отсрочке',

                'entries' => [
                    'delay-count'            => 'Величина отсрочки',
                    'delay-unit'             => 'Единица отсрочки',
                    'delay-from'             => 'Отсрочка от',
                    'delay-from-helper-text' => 'Начало отсчёта отсрочки',
                ],
            ],
        ],

        'note' => 'Примечание',
    ],
];
