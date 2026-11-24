<?php

return [
    'title' => 'Навыки',

    'navigation' => [
        'title' => 'Навыки',
    ],

    'form' => [
        'sections' => [
            'skill-details' => [
                'title' => 'Сведения о навыке',

                'fields' => [
                    'employee'       => 'Сотрудник',
                    'skill'          => 'Навык',
                    'skill-level'    => 'Уровень',
                    'skill-type'     => 'Тип навыка',
                ],
            ],
            'addition-information' => [
                'title' => 'Дополнительно',

                'fields' => [
                    'created-by' => 'Создал',
                    'updated-by' => 'Изменил',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'id'              => 'ID',
            'employee'        => 'Сотрудник',
            'skill'           => 'Навык',
            'skill-level'     => 'Уровень',
            'skill-type'      => 'Тип навыка',
            'user'            => 'Пользователь',
            'proficiency'     => 'Владение',
            'created-by'      => 'Создал',
            'created-at'      => 'Создано',
        ],

        'filters' => [
            'employee'        => 'Сотрудник',
            'skill'           => 'Навык',
            'skill-level'     => 'Уровень',
            'skill-type'      => 'Тип навыка',
            'user'            => 'Пользователь',
            'created-by'      => 'Создал',
            'created-at'      => 'Создано',
            'updated-at'      => 'Изменено',
        ],

        'groups' => [
            'employee'   => 'Сотрудник',
            'skill-type' => 'Тип навыка',
        ],
    ],

    'infolist' => [
        'sections' => [
            'skill-details' => [
                'title' => 'Сведения о навыке',

                'entries' => [
                    'employee'        => 'Сотрудник',
                    'skill'           => 'Навык',
                    'skill-level'     => 'Уровень',
                    'skill-type'      => 'Тип навыка',
                ],
            ],

            'additional-information' => [
                'title' => 'Дополнительно',

                'entries' => [
                    'created-by' => 'Создал',
                    'updated-by' => 'Изменил',
                ],
            ],
        ],
    ],
];
