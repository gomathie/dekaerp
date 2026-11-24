<?php

return [
    'title' => 'Типы занятости',

    'navigation' => [
        'title' => 'Типы занятости',
        'group' => 'Подбор персонала',
    ],

    'form' => [
        'fields' => [
            'name'    => 'Тип занятости',
            'code'    => 'Код',
            'country' => 'Страна',
        ],
    ],

    'table' => [
        'columns' => [
            'id'         => 'ID',
            'name'       => 'Тип занятости',
            'code'       => 'Код',
            'country'    => 'Страна',
            'created-by' => 'Создал',
            'created-at' => 'Создано',
            'updated-at' => 'Изменено',
        ],

        'filters' => [
            'name'       => 'Тип занятости',
            'country'    => 'Страна',
            'created-by' => 'Создал',
            'updated-at' => 'Изменено',
            'created-at' => 'Создано',
        ],

        'groups' => [
            'name'       => 'Тип занятости',
            'country'    => 'Страна',
            'code'       => 'Код',
            'created-by' => 'Создал',
            'created-at' => 'Создано',
            'updated-at' => 'Изменено',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Тип занятости изменён',
                    'body'  => 'Тип занятости изменён.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Тип занятости удалён',
                    'body'  => 'Тип занятости удалён.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Типы занятости удалены',
                    'body'  => 'Типы занятости удалены.',
                ],
            ],
        ],

        'empty-state-actions' => [
            'create' => [
                'notification' => [
                    'title' => 'Тип занятости создан',
                    'body'  => 'Тип занятости создан.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'name'    => 'Тип занятости',
            'code'    => 'Код',
            'country' => 'Страна',
        ],
    ],
];
