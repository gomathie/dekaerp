<?php

return [
    'title' => 'Метки',

    'navigation' => [
        'title' => 'Метки',
        'group' => 'Applications',
    ],

    'form' => [
        'fields' => [
            'name'             => 'Название',
            'name-placeholder' => 'Введите название метки',
            'color'            => 'Цвет',
        ],
    ],

    'table' => [
        'columns' => [
            'id'         => 'Ид.',
            'name'       => 'Название',
            'color'      => 'Цвет',
            'created-by' => 'Создано От',
            'created-at' => 'Создано',
            'updated-at' => 'Обновлено',
        ],

        'filters' => [
            'name'       => 'Название',
            'employee'   => 'Сотрудник',
            'created-by' => 'Создано От',
            'updated-at' => 'Обновлено',
            'created-at' => 'Создано',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Метки обновлено',
                    'body'  => 'метки есть обновлено successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Метки удалено',
                    'body'  => 'метки есть удалено successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Кандидат Категории удалено',
                    'body'  => 'кандидат Категории есть удалено successfully.',
                ],
            ],
        ],

        'empty-state-action' => [
            'create' => [
                'notification' => [
                    'title' => 'Метки создано',
                    'body'  => 'метки есть создано successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'name'  => 'Название',
        'color' => 'Цвет',
    ],
];
