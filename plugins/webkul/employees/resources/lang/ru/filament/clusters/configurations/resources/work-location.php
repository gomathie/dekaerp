<?php

return [
    'title' => 'Места работы',

    'navigation' => [
        'title' => 'Места работы',
        'group' => 'Сотрудник',
    ],

    'form' => [
        'name'            => 'Название',
        'company'         => 'Компания',
        'location-type'   => 'Тип места',
        'location-number' => 'Номер места',
        'status'          => 'Статус',
    ],

    'table' => [
        'columns' => [
            'id'              => 'ID',
            'name'            => 'Название',
            'status'          => 'Статус',
            'company'         => 'Компания',
            'location-type'   => 'Тип места',
            'location-number' => 'Номер места',
            'deleted-at'      => 'Удалено',
            'created-by'      => 'Создал',
            'created-at'      => 'Создано',
            'updated-at'      => 'Изменено',
        ],

        'filters' => [
            'name'            => 'Название',
            'status'          => 'Статус',
            'created-by'      => 'Создал',
            'company'         => 'Компания',
            'location-number' => 'Номер места',
            'location-type'   => 'Тип места',
            'updated-at'      => 'Изменено',
            'created-at'      => 'Создано',
        ],

        'groups' => [
            'name'          => 'Название',
            'status'        => 'Статус',
            'location-type' => 'Тип места',
            'company'       => 'Компания',
            'created-by'    => 'Создал',
            'created-at'    => 'Создано',
            'updated-at'    => 'Изменено',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Место работы обновлено',
                    'body'  => 'Место работы обновлено.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Место работы восстановлено',
                    'body'  => 'Место работы восстановлено.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Место работы удалено',
                    'body'  => 'Место работы удалено.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Место работы удалено навсегда',
                    'body'  => 'Место работы удалено навсегда.',
                ],
            ],

            'empty-state' => [
                'notification' => [
                    'title' => 'Место работы создано',
                    'body'  => 'Место работы создано.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Места работы удалены',
                    'body'  => 'Места работы удалены.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Места работы удалены навсегда',
                    'body'  => 'Места работы удалены навсегда.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'name'            => 'Название',
        'company'         => 'Компания',
        'location-type'   => 'Тип места',
        'location-number' => 'Номер места',
        'status'          => 'Статус',
    ],
];
