<?php

return [
    'navigation' => [
        'title' => 'Планы действий',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Общие сведения',
                'fields' => [
                    'name'       => 'Название',
                    'status'     => 'Статус',
                    'department' => 'Отдел',
                    'company'    => 'Компания',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'       => 'Название',
            'status'     => 'Статус',
            'department' => 'Отдел',
            'company'    => 'Компания',
            'manager'    => 'Руководитель',
            'created-by' => 'Создал',
            'created-at' => 'Создано',
            'updated-at' => 'Изменено',
        ],

        'filters' => [
            'name'           => 'Название',
            'plugin'         => 'Модуль',
            'activity-types' => 'Типы действий',
            'company'        => 'Компания',
            'department'     => 'Отдел',
            'is-active'      => 'Статус',
            'updated-at'     => 'Изменено',
            'created-at'     => 'Создано',
        ],

        'groups' => [
            'status'     => 'Статус',
            'name'       => 'Название',
            'created-by' => 'Создал',
            'created-at' => 'Создано',
            'updated-at' => 'Изменено',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'План действий восстановлен',
                    'body'  => 'План действий восстановлен.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'План действий удалён',
                    'body'  => 'План действий удалён.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'План действий удалён навсегда',
                    'body'  => 'План действий удалён навсегда.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Планы действий восстановлены',
                    'body'  => 'Планы действий восстановлены.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Планы действий удалены',
                    'body'  => 'Планы действий удалены.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Планы действий удалены навсегда',
                    'body'  => 'Планы действий удалены навсегда.',
                ],
            ],
        ],

        'activity-plan' => [
            'create' => [
                'notification' => [
                    'title' => 'План действий создан',
                    'body'  => 'План действий создан.',
                ],
            ],
        ],

        'empty-state' => [
            'create' => [
                'notification' => [
                    'title' => 'План действий создан',
                    'body'  => 'План действий создан.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'   => 'Общие сведения',
                'entries' => [
                    'name'       => 'Название',
                    'status'     => 'Статус',
                    'department' => 'Отдел',
                    'manager'    => 'Руководитель',
                    'company'    => 'Компания',
                ],
            ],
        ],
    ],
];
