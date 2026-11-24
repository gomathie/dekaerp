<?php

return [
    'modal' => [
        'title' => 'Рабочие часы',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Общие сведения',
                'fields' => [
                    'attendance-name' => 'Название',
                    'day-of-week'     => 'День недели',
                ],
            ],

            'timing-information' => [
                'title' => 'Время',

                'fields' => [
                    'day-period' => 'Часть дня',
                    'week-type'  => 'Тип недели',
                    'work-from'  => 'Начало работы',
                    'work-to'    => 'Окончание работы',
                ],
            ],

            'date-information' => [
                'title' => 'Даты',

                'fields' => [
                    'starting-date' => 'Дата начала',
                    'ending-date'   => 'Дата окончания',
                ],
            ],

            'additional-information' => [
                'title' => 'Дополнительно',

                'fields' => [
                    'durations-days' => 'Длительность (дни)',
                    'display-type'   => 'Тип отображения',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'          => 'Название',
            'day-of-week'   => 'День недели',
            'day-period'    => 'Часть дня',
            'work-from'     => 'Начало работы',
            'work-to'       => 'Окончание работы',
            'starting-date' => 'Дата начала',
            'ending-date'   => 'Дата окончания',
            'display-type'  => 'Тип отображения',
            'created-by'    => 'Создал',
            'created-at'    => 'Создано',
            'updated-at'    => 'Обновлено',
        ],

        'groups' => [
            'activity-type' => 'Тип действия',
            'assignment'    => 'Назначение',
            'assigned-to'   => 'Назначено',
            'interval'      => 'Интервал',
            'delay-unit'    => 'Единица задержки',
            'delay-from'    => 'Задержка от',
            'created-by'    => 'Создал',
            'created-at'    => 'Создано',
            'updated-at'    => 'Обновлено',
        ],

        'filters' => [
            'display-type' => 'Тип отображения',
            'day-of-week'  => 'День недели',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Рабочие часы обновлены',
                    'body'  => 'Рабочие часы успешно обновлены.',
                ],
            ],

            'create' => [
                'notification' => [
                    'title' => 'Рабочие часы созданы',
                    'body'  => 'Рабочие часы успешно созданы.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Рабочие часы удалены',
                    'body'  => 'Рабочие часы успешно удалены.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Рабочие часы восстановлены',
                    'body'  => 'Рабочие часы успешно восстановлены.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Рабочие часы удалены',
                    'body'  => 'Рабочие часы успешно удалены.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Рабочие часы удалены',
                    'body'  => 'Рабочие часы успешно удалены.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Рабочие часы удалены',
                    'body'  => 'Рабочие часы успешно удалены.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Общие сведения',

                'entries' => [
                    'name'        => 'Название',
                    'day-of-week' => 'День недели',
                ],
            ],

            'timing-information' => [
                'title' => 'Время',

                'entries' => [
                    'day-period' => 'Часть дня',
                    'week-type'  => 'Тип недели',
                    'work-from'  => 'Начало работы',
                    'work-to'    => 'Окончание работы',
                ],
            ],

            'date-information' => [
                'title' => 'Даты',

                'entries' => [
                    'starting-date' => 'Дата начала',
                    'ending-date'   => 'Дата окончания',
                ],
            ],

            'additional-information' => [
                'title' => 'Дополнительно',

                'entries' => [
                    'durations-days' => 'Длительность (дни)',
                    'display-type'   => 'Тип отображения',
                ],
            ],
        ],

        'note' => 'Примечание',
    ],
];
