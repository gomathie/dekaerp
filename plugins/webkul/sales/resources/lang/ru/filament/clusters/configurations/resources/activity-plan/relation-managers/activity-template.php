<?php

return [
    'form' => [
        'sections' => [
            'activity-details' => [
                'title' => 'Сведения об активности',

                'fields' => [
                    'activity-type' => 'Тип активности',
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
                    'delay-from'             => 'Задержка от',
                    'delay-from-helper-text' => 'Точка отсчёта задержки',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'activity-type' => 'Тип активности',
            'summary'       => 'Краткое описание',
            'assignment'    => 'Назначение',
            'assigned-to'   => 'Назначено',
            'interval'      => 'Интервал',
            'delay-unit'    => 'Единица задержки',
            'delay-from'    => 'Задержка от',
            'created-by'    => 'Кем создано',
            'created-at'    => 'Создано',
            'updated-at'    => 'Обновлено',
        ],

        'groups' => [
            'activity-type' => 'Тип активности',
            'assignment'    => 'Назначение',
            'assigned-to'   => 'Назначено',
            'interval'      => 'Интервал',
            'delay-unit'    => 'Единица задержки',
            'delay-from'    => 'Задержка от',
            'created-by'    => 'Кем создано',
            'created-at'    => 'Создано',
            'updated-at'    => 'Обновлено',
        ],

        'filters' => [
            'activity-type'   => 'Тип активности',
            'activity-status' => 'Статус активности',
            'has-delay'       => 'Есть задержка',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Шаблон активности обновлён',
                    'body'  => 'Шаблон активности успешно обновлён.',
                ],
            ],

            'create' => [
                'notification' => [
                    'title' => 'Шаблон активности создан',
                    'body'  => 'Шаблон активности успешно создан.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Шаблон активности удалён',
                    'body'  => 'Шаблон активности успешно удалён.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Шаблоны активностей удалены',
                    'body'  => 'Шаблоны активностей успешно удалены.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'activity-details' => [
                'title' => 'Сведения об активности',

                'entries' => [
                    'activity-type' => 'Тип активности',
                    'summary'       => 'Краткое описание',
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
                'title' => 'Сведения о задержке',

                'entries' => [
                    'delay-count'            => 'Величина задержки',
                    'delay-unit'             => 'Единица задержки',
                    'delay-from'             => 'Задержка от',
                    'delay-from-helper-text' => 'Точка отсчёта задержки',
                ],
            ],
        ],

        'note' => 'Примечание',
    ],
];
