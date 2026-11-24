<?php

return [
    'navigation' => [
        'title' => 'Планы активностей',
        'group' => 'Активности',
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
            'created-by' => 'Кем создано',
            'created-at' => 'Создано',
            'updated-at' => 'Обновлено',
        ],

        'filters' => [
            'name'           => 'Название',
            'plugin'         => 'Плагин',
            'activity-types' => 'Типы активностей',
            'company'        => 'Компания',
            'department'     => 'Отдел',
            'is-active'      => 'Статус',
            'updated-at'     => 'Обновлено',
            'created-at'     => 'Создано',
        ],

        'groups' => [
            'status'     => 'Статус',
            'name'       => 'Название',
            'created-by' => 'Кем создано',
            'created-at' => 'Создано',
            'updated-at' => 'Обновлено',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'План активностей восстановлен',
                    'body'  => 'План активностей успешно восстановлен.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'План активностей удалён',
                    'body'  => 'План активностей успешно удалён.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'План активностей удалён навсегда',
                    'body'  => 'План активностей успешно удалён навсегда.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Планы активностей восстановлены',
                    'body'  => 'Планы активностей успешно восстановлены.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Планы активностей удалены',
                    'body'  => 'Планы активностей успешно удалены.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Планы активностей удалены навсегда',
                    'body'  => 'Планы активностей успешно удалены навсегда.',
                ],
            ],
        ],

        'empty-state' => [
            'create' => [
                'notification' => [
                    'title' => 'План активностей создан',
                    'body'  => 'План активностей успешно создан.',
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
