<?php

return [
    'form' => [
        'name' => 'Название',
    ],

    'table' => [
        'columns' => [
            'name'       => 'Название',
            'created-at' => 'Создано',
            'updated-at' => 'Изменено',
        ],

        'groups' => [
            'created-at' => 'Создано',
        ],

        'filters' => [
            'deleted-records' => 'Удалённые записи',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Навык обновлён',
                    'body'  => 'Навык обновлён.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Навык восстановлен',
                    'body'  => 'Навык восстановлен.',
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

            'force-delete' => [
                'notification' => [
                    'title' => 'Навыки удалены навсегда',
                    'body'  => 'Навыки удалены навсегда.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Навыки восстановлены',
                    'body'  => 'Навыки восстановлены.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'name' => 'Название',
        ],
    ],
];
