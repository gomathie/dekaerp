<?php

return [
    'title' => 'Employment Types',

    'navigation' => [
        'title' => 'Employment Types',
        'group' => 'Подбор',
    ],

    'form' => [
        'fields' => [
            'name'    => 'Employment Тип',
            'code'    => 'Код',
            'country' => 'Страна',
        ],
    ],

    'table' => [
        'columns' => [
            'id'         => 'Ид.',
            'name'       => 'Employment Тип',
            'code'       => 'Код',
            'country'    => 'Страна',
            'created-by' => 'Создано От',
            'created-at' => 'Создано',
            'updated-at' => 'Обновлено',
        ],

        'filters' => [
            'name'       => 'Employment Тип',
            'country'    => 'Страна',
            'created-by' => 'Создано От',
            'updated-at' => 'Обновлено',
            'created-at' => 'Создано',
        ],

        'groups' => [
            'name'       => 'Employment Тип',
            'country'    => 'Страна',
            'code'       => 'Код',
            'created-by' => 'Создано От',
            'created-at' => 'Создано',
            'updated-at' => 'Обновлено',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Employment Тип',
                    'body'  => 'Employment Тип есть edited successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Employment Тип удалено',
                    'body'  => 'Employment Тип есть удалено successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Employment Types удалено',
                    'body'  => 'Employment Types есть удалено successfully.',
                ],
            ],
        ],

        'empty-state-actions' => [
            'create' => [
                'notification' => [
                    'title' => 'Employment Types',
                    'body'  => 'Employment Types есть создано successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'name'    => 'Employment Тип',
            'code'    => 'Код',
            'country' => 'Страна',
        ],
    ],
];
