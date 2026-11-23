<?php

return [
    'title' => 'Work Места',

    'navigation' => [
        'title' => 'Work Места',
        'group' => 'Сотрудник',
    ],

    'form' => [
        'name'            => 'Название',
        'company'         => 'Компания',
        'location-type'   => 'Место Тип',
        'location-number' => 'Место Номер',
        'status'          => 'Статус',
    ],

    'table' => [
        'columns' => [
            'id'              => 'Ид.',
            'name'            => 'Название',
            'status'          => 'Статус',
            'company'         => 'Компания',
            'location-type'   => 'Место Тип',
            'location-number' => 'Место Номер',
            'deleted-at'      => 'Удалено',
            'created-by'      => 'Создано От',
            'created-at'      => 'Создано',
            'updated-at'      => 'Обновлено',
        ],

        'filters' => [
            'name'            => 'Название',
            'status'          => 'Статус',
            'created-by'      => 'Создано От',
            'company'         => 'Компания',
            'location-number' => 'Место Номер',
            'location-type'   => 'Место Тип',
            'updated-at'      => 'Обновлено',
            'created-at'      => 'Создано',
        ],

        'groups' => [
            'name'          => 'Название',
            'status'        => 'Статус',
            'location-type' => 'Место Тип',
            'company'       => 'Компания',
            'created-by'    => 'Создано От',
            'created-at'    => 'Создано',
            'updated-at'    => 'Обновлено',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Work Место обновлено',
                    'body'  => 'work Место есть обновлено successfully.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Work Место restored',
                    'body'  => 'work Место есть restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Work Место удалено',
                    'body'  => 'work Место есть удалено successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Work Место force удалено',
                    'body'  => 'work Место есть force удалено successfully.',
                ],
            ],

            'empty-state' => [
                'notification' => [
                    'title' => 'Work Место создано',
                    'body'  => 'Work Место есть создано successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Work Места удалено',
                    'body'  => 'work Места есть удалено successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Work Места force удалено',
                    'body'  => 'work Места есть force удалено successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'name'            => 'Название',
        'company'         => 'Компания',
        'location-type'   => 'Место Тип',
        'location-number' => 'Место Номер',
        'status'          => 'Статус',
    ],
];
