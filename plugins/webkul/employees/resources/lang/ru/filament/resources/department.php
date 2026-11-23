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
                'title' => 'Общее Сведения',

                'fields' => [
                    'name'                => 'Название',
                    'manager'             => 'Руководитель',
                    'parent-department'   => 'Parent Отдел',
                    'manager-placeholder' => 'Выбрать Руководитель',
                    'company'             => 'Компания',
                    'company-placeholder' => 'Выбрать Компания',
                    'color'               => 'Цвет',
                ],
            ],

            'additional' => [
                'title'       => 'Additional Сведения',
                'description' => 'Additional сведения около этот отдел.',
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
            'updated-at' => 'Обновлено',
            'created-at' => 'Создано',
        ],

        'filters' => [
            'name'         => 'Название',
            'manager-name' => 'Руководитель',
            'company-name' => 'Компания',
            'updated-at'   => 'Обновлено',
            'created-at'   => 'Создано',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Отдел restored',
                    'body'  => 'отдел есть restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Отдел удалено',
                    'body'  => 'отдел есть удалено successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Отдел force удалено',
                    'body'  => 'отдел есть force удалено successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Отделы restored',
                    'body'  => 'отделы есть restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Отделы удалено',
                    'body'  => 'отделы есть удалено successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Отделы force удалено',
                    'body'  => 'отделы есть force удалено successfully.',
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
                    'hierarchy-title' => 'Отдел Organization',
                ],
            ],
        ],
    ],
];
