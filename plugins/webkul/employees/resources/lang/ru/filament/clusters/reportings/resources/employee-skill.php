<?php

return [
    'title' => 'Skills',

    'navigation' => [
        'title' => 'Skills',
    ],

    'form' => [
        'sections' => [
            'skill-details' => [
                'title' => 'Skill Подробности',

                'fields' => [
                    'employee'       => 'Сотрудник',
                    'skill'          => 'Skill',
                    'skill-level'    => 'Level',
                    'skill-type'     => 'Skill Тип',
                ],
            ],
            'addition-information' => [
                'title' => 'Additional Сведения',

                'fields' => [
                    'created-by' => 'Создано От',
                    'updated-by' => 'Обновлено От',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'id'              => 'Ид.',
            'employee'        => 'Сотрудник',
            'skill'           => 'Skill',
            'skill-level'     => 'Level',
            'skill-type'      => 'Skill Тип',
            'user'            => 'Пользователь',
            'proficiency'     => 'Proficiency',
            'created-by'      => 'Создано От',
            'created-at'      => 'Создано',
        ],

        'filters' => [
            'employee'        => 'Сотрудник',
            'skill'           => 'Skill',
            'skill-level'     => 'Level',
            'skill-type'      => 'Skill Тип',
            'user'            => 'Пользователь',
            'created-by'      => 'Создано От',
            'created-at'      => 'Создано',
            'updated-at'      => 'Обновлено',
        ],

        'groups' => [
            'employee'   => 'Сотрудник',
            'skill-type' => 'Skill Тип',
        ],
    ],

    'infolist' => [
        'sections' => [
            'skill-details' => [
                'title' => 'Skill Подробности',

                'entries' => [
                    'employee'        => 'Сотрудник',
                    'skill'           => 'Skill',
                    'skill-level'     => 'Level',
                    'skill-type'      => 'Skill Тип',
                ],
            ],

            'additional-information' => [
                'title' => 'Additional Сведения',

                'entries' => [
                    'created-by' => 'Создано От',
                    'updated-by' => 'Обновлено От',
                ],
            ],
        ],
    ],
];
