<?php

return [
    'form' => [
        'name'          => 'Название',
        'level'         => 'Уровень',
        'default-level' => 'Уровень по умолчанию',
    ],

    'table' => [
        'columns' => [
            'name'          => 'Название',
            'level'         => 'Уровень',
            'default-level' => 'Уровень по умолчанию',
            'created-at'    => 'Создано',
            'updated-at'    => 'Изменено',
        ],

        'groups' => [
            'created-at' => 'Создано',
        ],

        'filters' => [
            'deleted-records' => 'Удалённые записи',
        ],

        'actions' => [
            'create' => [
                'notification' => [
                    'title' => 'Уровень навыка создан',
                    'body'  => 'Уровень навыка создан.',
                ],
            ],

            'edit' => [
                'notification' => [
                    'title' => 'Уровень навыка обновлён',
                    'body'  => 'Уровень навыка обновлён.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Уровень навыка восстановлен',
                    'body'  => 'Уровень навыка восстановлен.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Уровень навыка удалён',
                    'body'  => 'Уровень навыка удалён.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Уровни навыков удалены',
                    'body'  => 'Уровни навыков удалены.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Уровни навыков удалены навсегда',
                    'body'  => 'Уровни навыков удалены навсегда.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Уровни навыков восстановлены',
                    'body'  => 'Уровни навыков восстановлены.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'name'          => 'Название',
            'level'         => 'Уровень',
            'default-level' => 'Уровень по умолчанию',
        ],
    ],
];
