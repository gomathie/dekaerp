<?php

return [
    'form' => [
        'name' => 'Название',
    ],

    'table' => [
        'columns' => [
            'name'       => 'Название',
            'created-at' => 'Создано',
            'updated-at' => 'Обновлено',
        ],

        'groups' => [
            'created-at' => 'Создано',
        ],

        'filters' => [
            'deleted-records' => 'Удалено Записи',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Skill обновлено',
                    'body'  => 'skill есть обновлено successfully.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Skill restored',
                    'body'  => 'skill есть restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Skill удалено',
                    'body'  => 'skill есть удалено successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Skills удалено',
                    'body'  => 'skills есть удалено successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Skills force удалено',
                    'body'  => 'skills есть force удалено successfully.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Skills force restored',
                    'body'  => 'skills есть force restored successfully.',
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
