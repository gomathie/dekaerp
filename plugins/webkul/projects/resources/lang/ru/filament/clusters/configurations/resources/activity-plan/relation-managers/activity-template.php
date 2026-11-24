<?php

return [
    'form' => [
        'sections' => [
            'activity-details' => [
                'title' => 'Сведения о действии',

                'fields' => [
                    'activity-type' => 'Тип действия',
                    'summary'       => 'Краткое описание',
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
                'title' => 'Сведения о задержке',

                'fields' => [
                    'delay-count'            => 'Величина задержки',
                    'delay-unit'             => 'Единица задержки',
                    'delay-from'             => 'Отсчёт задержки от',
                    'delay-from-helper-text' => 'Точка отсчёта задержки',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'activity-type' => 'Тип действия',
            'summary'       => 'Краткое описание',
            'assignment'    => 'Назначение',
            'assigned-to'   => 'Назначено',
            'interval'      => 'Интервал',
            'delay-unit'    => 'Единица задержки',
            'delay-from'    => 'Отсчёт задержки от',
            'created-by'    => 'Создал',
            'created-at'    => 'Дата создания',
            'updated-at'    => 'Дата изменения',
        ],

        'groups' => [
            'activity-type' => 'Тип действия',
            'assignment'    => 'Назначение',
            'assigned-to'   => 'Назначено',
            'interval'      => 'Интервал',
            'delay-unit'    => 'Единица задержки',
            'delay-from'    => 'Отсчёт задержки от',
            'created-by'    => 'Создал',
            'created-at'    => 'Дата создания',
            'updated-at'    => 'Дата изменения',
        ],

        'filters' => [
            'activity-type'   => 'Тип действия',
            'activity-status' => 'Статус действия',
            'has-delay'       => 'С задержкой',
        ],

        'header-actions' => [
            'create' => [
                'notification' => [
                    'title' => 'Шаблон плана действий создан',
                    'body'  => 'Шаблон плана действий успешно создан.',
                ],
            ],
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Шаблон действия обновлён',
                    'body'  => 'Шаблон действия успешно обновлён.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Шаблон действия удалён',
                    'body'  => 'Шаблон действия успешно удалён.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Шаблоны действий удалены',
                    'body'  => 'Шаблоны действий успешно удалены.',
                ],
            ],
        ],
    ],
];
