<?php

return [
    'title' => 'Skill Types',

    'navigation' => [
        'title' => 'Skill Types',
        'group' => 'Сотрудник',
    ],

    'form' => [
        'sections' => [
            'fields' => [
                'name'             => 'Название',
                'name-placeholder' => 'Введите skill тип название',
                'color'            => 'Цвет',
                'status'           => 'Статус',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'id'         => 'Ид.',
            'name'       => 'Skill Тип',
            'status'     => 'Статус',
            'color'      => 'Цвет',
            'skills'     => 'Skills',
            'levels'     => 'Levels',
            'created-by' => 'Создано От',
            'created-at' => 'Создано',
            'updated-at' => 'Обновлено',
        ],

        'filters' => [
            'skill-levels' => 'Skill Levels',
            'skills'       => 'Skills',
            'created-by'   => 'Создано От',
            'status'       => 'Статус',
            'updated-at'   => 'Обновлено',
            'created-at'   => 'Создано',
        ],

        'groups' => [
            'name'       => 'Skill Тип',
            'color'      => 'Цвет',
            'status'     => 'Статус',
            'created-by' => 'Создано От',
            'created-at' => 'Создано',
            'updated-at' => 'Обновлено',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Skill Тип restored',
                    'body'  => 'Skill Тип есть restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Skill Тип удалено',
                    'body'  => 'Skill Тип есть удалено successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Skill Types restored',
                    'body'  => 'Skill Types есть restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Skill Types удалено',
                    'body'  => 'Skill Types есть удалено successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Skill Types force удалено',
                    'body'  => 'Skill Types есть force удалено successfully.',
                ],
            ],
        ],

        'empty-state-actions' => [
            'create' => [
                'notification' => [
                    'title' => 'Skill Types',
                    'body'  => 'Skill Types есть создано successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'entries' => [
                'name'   => 'Skill Тип',
                'color'  => 'Цвет',
                'status' => 'Статус',
            ],
        ],
    ],
];
