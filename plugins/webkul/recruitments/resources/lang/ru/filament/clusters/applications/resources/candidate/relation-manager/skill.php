<?php

return [
    'form' => [
        'sections' => [
            'fields' => [
                'skill-type'  => 'Тип навыка',
                'skill'       => 'Навык',
                'skill-level' => 'Уровень навыка',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'skill-type'    => 'Тип навыка',
            'skill'         => 'Навык',
            'skill-level'   => 'Уровень навыка',
            'level-percent' => 'Уровень, %',
            'created-by'    => 'Создал',
            'user'          => 'Пользователь',
            'created-at'    => 'Дата создания',
        ],

        'groups' => [
            'skill-type' => 'Тип навыка',
        ],

        'header-actions' => [
            'add-skill' => 'Добавить навык',
        ],

        'filters' => [
            'activity-type'   => 'Тип действия',
            'activity-status' => 'Статус действия',
            'has-delay'       => 'С задержкой',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Навык обновлён',
                    'body'  => 'Навык успешно обновлён.',
                ],
            ],

            'create' => [
                'notification' => [
                    'title' => 'Навык создан',
                    'body'  => 'Навык успешно создан.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Навык удалён',
                    'body'  => 'Навык успешно удалён.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Навыки удалены',
                    'body'  => 'Навыки успешно удалены.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'skill-type'    => 'Тип навыка',
            'skill'         => 'Навык',
            'skill-level'   => 'Уровень навыка',
            'level-percent' => 'Уровень, %',
        ],
    ],
];
