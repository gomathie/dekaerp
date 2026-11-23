<?php

return [
    'form' => [
        'sections' => [
            'fields' => [
                'skill-type'  => 'Skill Тип',
                'skill'       => 'Skill',
                'skill-level' => 'Skill Level',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'skill-type'    => 'Skill Тип',
            'skill'         => 'Skill',
            'skill-level'   => 'Skill Level',
            'level-percent' => 'Level Процент',
            'created-by'    => 'Создано От',
            'user'          => 'Пользователь',
            'created-at'    => 'Создано',
        ],

        'groups' => [
            'skill-type' => 'Skill Тип',
        ],

        'header-actions' => [
            'add-skill' => 'Добавить Skill',
        ],

        'filters' => [
            'activity-type'   => 'Активность Тип',
            'activity-status' => 'Активность Статус',
            'has-delay'       => 'Есть Delay',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Skill обновлено',
                    'body'  => 'skill есть обновлено successfully.',
                ],
            ],

            'create' => [
                'notification' => [
                    'title' => 'Skill создано',
                    'body'  => 'skill есть создано successfully.',
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
        ],
    ],

    'infolist' => [
        'entries' => [
            'skill-type'    => 'Skill Тип',
            'skill'         => 'Skill',
            'skill-level'   => 'Skill Level',
            'level-percent' => 'Level Процент',
        ],
    ],
];
