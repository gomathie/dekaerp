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
                    'attendance-name' => 'Название интервала',
                    'attendance-name' => 'Название интервала',
                    'day-of-week'     => 'День недели',
                ],
            ],

            'timing-information' => [
                'title' => 'Время',

                'fields' => [
                    'day-period' => 'Часть дня',
                    'week-type'  => 'Тип недели',
                    'work-from'  => 'Работа с',
                    'work-to'    => 'Работа до',
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
                    'durations-days' => 'Длительность (дней)',
                    'display-type'   => 'Тип отображения',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'          => 'Название интервала',
            'day-of-week'   => 'День недели',
            'day-period'    => 'Часть дня',
            'work-from'     => 'Работа с',
            'work-to'       => 'Работа до',
            'starting-date' => 'Дата начала',
            'ending-date'   => 'Дата окончания',
            'display-type'  => 'Тип отображения',
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
            'display-type' => 'Тип отображения',
            'day-of-week'  => 'День недели',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Рабочие часы обновлены',
                    'body'  => 'Рабочие часы обновлены.',
                ],
            ],

            'create' => [
                'notification' => [
                    'title' => 'Рабочие часы созданы',
                    'body'  => 'Рабочие часы созданы.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Рабочие часы удалены',
                    'body'  => 'Рабочие часы удалены.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Рабочие часы восстановлены',
                    'body'  => 'Рабочие часы восстановлены.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Рабочие часы удалены',
                    'body'  => 'Рабочие часы удалены.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Рабочие часы удалены',
                    'body'  => 'Рабочие часы удалены.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Рабочие часы удалены',
                    'body'  => 'Рабочие часы удалены.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Общие сведения',

                'entries' => [
                    'name'        => 'Название интервала',
                    'day-of-week' => 'День недели',
                ],
            ],

            'timing-information' => [
                'title' => 'Время',

                'entries' => [
                    'day-period' => 'Часть дня',
                    'week-type'  => 'Тип недели',
                    'work-from'  => 'Работа с',
                    'work-to'    => 'Работа до',
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
                    'durations-days' => 'Длительность (дней)',
                    'display-type'   => 'Тип отображения',
                ],
            ],
        ],

        'note' => 'Примечание',
    ],
];
