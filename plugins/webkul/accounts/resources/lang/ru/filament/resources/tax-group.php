<?php

return [
    'form' => [
        'sections' => [
            'fields' => [
                'company'            => 'Компания',
                'country'            => 'Страна',
                'name'               => 'Название',
                'preceding-subtotal' => 'Предшествующий подытог',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'company'            => 'Компания',
            'country'            => 'Страна',
            'created-by'         => 'Создал',
            'name'               => 'Название',
            'preceding-subtotal' => 'Предшествующий подытог',
            'created-at'         => 'Создано',
            'updated-at'         => 'Обновлено',
        ],

        'groups' => [
            'name'       => 'Название',
            'company'    => 'Компания',
            'country'    => 'Страна',
            'created-by' => 'Создал',
            'created-at' => 'Создано',
            'updated-at' => 'Обновлено',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Группа налогов удалена',
                        'body'  => 'Группа налогов успешно удалена.',
                    ],

                    'error' => [
                        'title' => 'Не удалось удалить группу налогов',
                        'body'  => 'Группу налогов нельзя удалить: она сейчас используется.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Группы налогов удалены',
                        'body'  => 'Группы налогов успешно удалены.',
                    ],

                    'error' => [
                        'title' => 'Не удалось удалить группы налогов',
                        'body'  => 'Группы налогов нельзя удалить: они сейчас используются.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'entries' => [
                'company'            => 'Компания',
                'country'            => 'Страна',
                'name'               => 'Название',
                'preceding-subtotal' => 'Предшествующий подытог',
            ],
        ],
    ],
];
