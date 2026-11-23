<?php

return [
    'form' => [
        'name'          => 'Название',
        'level'         => 'Level',
        'default-level' => 'По умолчанию Level',
    ],

    'table' => [
        'columns' => [
            'name'          => 'Название',
            'level'         => 'Level',
            'default-level' => 'По умолчанию Level',
            'created-at'    => 'Создано',
            'updated-at'    => 'Обновлено',
        ],

        'groups' => [
            'created-at' => 'Создано',
        ],

        'filters' => [
            'deleted-records' => 'Удалено Записи',
        ],

        'actions' => [
            'create' => [
                'notification' => [
                    'title' => 'Skill Level создано',
                    'body'  => 'skill level есть создано successfully.',
                ],
            ],

            'edit' => [
                'notification' => [
                    'title' => 'Skill Level обновлено',
                    'body'  => 'skill level есть обновлено successfully.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Skill Level restored',
                    'body'  => 'skill level есть restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Skill Level удалено',
                    'body'  => 'skill level есть удалено successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Skill Levels удалено',
                    'body'  => 'skills есть удалено successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Skill Levels force удалено',
                    'body'  => 'skills есть force удалено successfully.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Skill Levels force restored',
                    'body'  => 'skills есть force restored successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'name'          => 'Название',
            'level'         => 'Level',
            'default-level' => 'По умолчанию Level',
        ],
    ],
];
