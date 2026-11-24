<?php

return [
    'navigation' => [
        'title' => 'Планы действий',
        'group' => 'Действия',
    ],

    'global-search' => [
        'name'         => 'Отдел',
        'department'   => 'Отдел',
        'manager'      => 'Руководитель',
        'company'      => 'Компания',
        'plugin'       => 'Модуль',
        'creator-name' => 'Создал',
    ],

    'table' => [
        'columns' => [
            'name'       => 'Название',
            'status'     => 'Статус',
            'department' => 'Отдел',
            'company'    => 'Компания',
            'manager'    => 'Руководитель',
            'created-by' => 'Создал',
            'created-at' => 'Дата создания',
            'updated-at' => 'Дата изменения',
        ],

        'filters' => [
            'name'           => 'Название',
            'plugin'         => 'Модуль',
            'activity-types' => 'Типы действий',
            'company'        => 'Компания',
            'department'     => 'Отдел',
            'is-active'      => 'Статус',
            'updated-at'     => 'Дата изменения',
            'created-at'     => 'Дата создания',
        ],

        'groups' => [
            'status'     => 'Статус',
            'name'       => 'Название',
            'created-by' => 'Создал',
            'created-at' => 'Дата создания',
            'updated-at' => 'Дата изменения',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'План действий восстановлен',
                    'body'  => 'План действий успешно восстановлен.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'План действий удалён',
                    'body'  => 'План действий успешно удалён.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'План действий удалён безвозвратно',
                    'body'  => 'План действий удалён безвозвратно.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Планы действий восстановлены',
                    'body'  => 'Планы действий успешно восстановлены.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Планы действий удалены',
                    'body'  => 'Планы действий успешно удалены.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Планы действий удалены безвозвратно',
                    'body'  => 'Планы действий удалены безвозвратно.',
                ],
            ],
        ],

        'empty-state' => [
            'create' => [
                'notification' => [
                    'title' => 'План действий создан',
                    'body'  => 'План действий успешно создан.',
                ],
            ],
        ],
    ],
];
