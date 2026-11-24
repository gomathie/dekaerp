<?php

return [
    'title' => 'Отделы',

    'navigation' => [
        'title' => 'Отделы',
    ],

    'global-search' => [
        'department-manager' => 'Руководитель',
        'company'            => 'Компания',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Общие сведения',

                'fields' => [
                    'name'                => 'Название',
                    'manager'             => 'Руководитель',
                    'parent-department'   => 'Вышестоящий отдел',
                    'manager-placeholder' => 'Выберите руководителя',
                    'company'             => 'Компания',
                    'company-placeholder' => 'Выберите компанию',
                    'color'               => 'Цвет',
                ],
            ],

            'additional' => [
                'title'       => 'Дополнительно',
                'description' => 'Дополнительные сведения об этом отделе.',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'         => 'Название',
            'manager-name' => 'Руководитель',
            'company-name' => 'Компания',
        ],

        'groups' => [
            'name'       => 'Название',
            'manager'    => 'Руководитель',
            'company'    => 'Компания',
            'updated-at' => 'Изменено',
            'created-at' => 'Создано',
        ],

        'filters' => [
            'name'         => 'Название',
            'manager-name' => 'Руководитель',
            'company-name' => 'Компания',
            'updated-at'   => 'Изменено',
            'created-at'   => 'Создано',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Отдел восстановлен',
                    'body'  => 'Отдел восстановлен.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Отдел удалён',
                    'body'  => 'Отдел удалён.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Отдел удалён навсегда',
                    'body'  => 'Отдел удалён навсегда.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Отделы восстановлены',
                    'body'  => 'Отделы восстановлены.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Отделы удалены',
                    'body'  => 'Отделы удалены.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Отделы удалены навсегда',
                    'body'  => 'Отделы удалены навсегда.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Общее',

                'entries' => [
                    'name'            => 'Название',
                    'manager'         => 'Руководитель',
                    'company'         => 'Компания',
                    'color'           => 'Цвет',
                    'hierarchy-title' => 'Структура отдела',
                ],
            ],
        ],
    ],
];
