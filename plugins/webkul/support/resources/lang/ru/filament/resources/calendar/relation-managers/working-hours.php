<?php

return [
    'modal' => [
        'title' => 'Working Часы',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Общее Сведения',
                'fields' => [
                    'attendance-name' => 'Посещаемость Название',
                    'day-of-week'     => 'День Неделя',
                ],
            ],

            'timing-information' => [
                'title' => 'Timing Сведения',

                'fields' => [
                    'day-period' => 'День Periods',
                    'week-type'  => 'Неделя Тип',
                    'work-from'  => 'Work От',
                    'work-to'    => 'Work До',
                ],
            ],

            'date-information' => [
                'title' => 'Дата Сведения',

                'fields' => [
                    'starting-date' => 'Starting Дата',
                    'ending-date'   => 'Ending Дата',
                ],
            ],

            'additional-information' => [
                'title' => 'Additional Сведения',

                'fields' => [
                    'durations-days' => 'Duration (Дни)',
                    'display-type'   => 'Display Тип',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'          => 'Посещаемость Название',
            'day-of-week'   => 'День Неделя',
            'day-period'    => 'День Periods',
            'work-from'     => 'Work От',
            'work-to'       => 'Work До',
            'starting-date' => 'Starting Дата',
            'ending-date'   => 'Ending Дата',
            'display-type'  => 'Display Тип',
            'created-by'    => 'Создано От',
            'created-at'    => 'Создано',
            'updated-at'    => 'Обновлено',
        ],

        'groups' => [
            'activity-type' => 'Активность Тип',
            'assignment'    => 'Assignment',
            'assigned-to'   => 'Назначен До',
            'interval'      => 'Interval',
            'delay-unit'    => 'Delay Единица',
            'delay-from'    => 'Delay От',
            'created-by'    => 'Создано От',
            'created-at'    => 'Создано',
            'updated-at'    => 'Обновлено',
        ],

        'filters' => [
            'display-type' => 'Display Тип',
            'day-of-week'  => 'День Неделя',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Working Часы обновлено',
                    'body'  => 'working часы есть обновлено successfully.',
                ],
            ],

            'create' => [
                'notification' => [
                    'title' => 'Working Часы создано',
                    'body'  => 'working часы есть создано successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Working Часы удалено',
                    'body'  => 'working часы есть удалено successfully.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Working Часы restored',
                    'body'  => 'working часы есть restored successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Working Часы удалено',
                    'body'  => 'working часы есть удалено successfully.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Working Часы удалено',
                    'body'  => 'working часы есть удалено successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Working Часы удалено',
                    'body'  => 'working часы есть удалено successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Общее Сведения',

                'entries' => [
                    'name'        => 'Посещаемость Название',
                    'day-of-week' => 'День Неделя',
                ],
            ],

            'timing-information' => [
                'title' => 'Timing Сведения',

                'entries' => [
                    'day-period' => 'День Periods',
                    'week-type'  => 'Неделя Тип',
                    'work-from'  => 'Work От',
                    'work-to'    => 'Work До',
                ],
            ],

            'date-information' => [
                'title' => 'Дата Сведения',

                'entries' => [
                    'starting-date' => 'Starting Дата',
                    'ending-date'   => 'Ending Дата',
                ],
            ],

            'additional-information' => [
                'title' => 'Additional Сведения',

                'entries' => [
                    'durations-days' => 'Duration (Дни)',
                    'display-type'   => 'Display Тип',
                ],
            ],
        ],

        'note' => 'Заметка',
    ],
];
