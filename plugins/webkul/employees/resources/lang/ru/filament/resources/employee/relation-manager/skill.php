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
            'created-at'    => 'Создано',
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
            'has-delay'       => 'С отсрочкой',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Навык обновлён',
                    'body'  => 'Навык обновлён.',
                ],
            ],

            'create' => [
                'notification' => [
                    'title' => 'Навык создан',
                    'body'  => 'Навык создан.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Навык удалён',
                    'body'  => 'Навык удалён.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Навыки удалены',
                    'body'  => 'Навыки удалены.',
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
