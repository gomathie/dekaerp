<?php

return [
    'title' => 'Типы навыков',

    'navigation' => [
        'title' => 'Типы навыков',
        'group' => 'Сотрудник',
    ],

    'form' => [
        'sections' => [
            'fields' => [
                'name'             => 'Название',
                'name-placeholder' => 'Введите название типа навыка',
                'color'            => 'Цвет',
                'status'           => 'Статус',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'id'         => 'ID',
            'name'       => 'Тип навыка',
            'status'     => 'Статус',
            'color'      => 'Цвет',
            'skills'     => 'Навыки',
            'levels'     => 'Уровни',
            'created-by' => 'Создал',
            'created-at' => 'Создано',
            'updated-at' => 'Изменено',
        ],

        'filters' => [
            'skill-levels' => 'Уровни навыков',
            'skills'       => 'Навыки',
            'created-by'   => 'Создал',
            'status'       => 'Статус',
            'updated-at'   => 'Изменено',
            'created-at'   => 'Создано',
        ],

        'groups' => [
            'name'       => 'Тип навыка',
            'color'      => 'Цвет',
            'status'     => 'Статус',
            'created-by' => 'Создал',
            'created-at' => 'Создано',
            'updated-at' => 'Изменено',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Тип навыка восстановлен',
                    'body'  => 'Тип навыка восстановлен.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Тип навыка удалён',
                    'body'  => 'Тип навыка удалён.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Типы навыков восстановлены',
                    'body'  => 'Типы навыков восстановлены.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Типы навыков удалены',
                    'body'  => 'Типы навыков удалены.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Типы навыков удалены навсегда',
                    'body'  => 'Типы навыков удалены навсегда.',
                ],
            ],
        ],

        'empty-state-actions' => [
            'create' => [
                'notification' => [
                    'title' => 'Тип навыка создан',
                    'body'  => 'Тип навыка создан.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'entries' => [
                'name'   => 'Тип навыка',
                'color'  => 'Цвет',
                'status' => 'Статус',
            ],
        ],
    ],
];
