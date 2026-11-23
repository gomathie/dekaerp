<?php

return [
    'form' => [
        'sections' => [
            'activity-details' => [
                'title' => 'Активность Подробности',

                'fields' => [
                    'activity-type' => 'Активность Тип',
                    'summary'       => 'Сводка',
                    'note'          => 'Заметка',
                ],
            ],

            'assignment' => [
                'title' => 'Assignment',

                'fields' => [
                    'assignment' => 'Assignment',
                    'assignee'   => 'Assignee',
                ],
            ],

            'delay-information' => [
                'title' => 'Delay Сведения',

                'fields' => [
                    'delay-count'            => 'Delay Количество',
                    'delay-unit'             => 'Delay Единица',
                    'delay-from'             => 'Delay От',
                    'delay-from-helper-text' => 'Источник delay calculation',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'activity-type' => 'Активность Тип',
            'summary'       => 'Сводка',
            'assignment'    => 'Assignment',
            'assigned-to'   => 'Назначен До',
            'interval'      => 'Interval',
            'delay-unit'    => 'Delay Единица',
            'delay-from'    => 'Delay От',
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
            'activity-type'   => 'Активность Тип',
            'activity-status' => 'Активность Статус',
            'has-delay'       => 'Есть Delay',
        ],

        'header-actions' => [
            'create' => [
                'notification' => [
                    'title' => 'Активность plan шаблон создано',
                    'body'  => 'активность plan шаблон есть создано successfully.',
                ],
            ],
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Активность шаблон обновлено',
                    'body'  => 'активность шаблон есть обновлено successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Активность шаблон удалено',
                    'body'  => 'активность шаблон есть удалено successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Активность шаблоны удалено',
                    'body'  => 'активность шаблоны есть удалено successfully.',
                ],
            ],
        ],
    ],
];
