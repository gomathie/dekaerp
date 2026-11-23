<?php

return [
    'title' => 'Mandatory Дни',

    'model-label' => 'Mandatory День',

    'navigation' => [
        'title' => 'Mandatory Holidays',
    ],

    'form' => [
        'fields' => [
            'name'       => 'Название',
            'start-date' => 'Начать Дата',
            'end-date'   => 'Конец Дата',
            'color'      => 'Цвет',
        ],
    ],

    'table' => [
        'columns' => [
            'name'         => 'Название',
            'company-name' => 'Компания Название',
            'created-by'   => 'Создано От',
            'start-date'   => 'Начать Дата',
            'end-date'     => 'Конец Дата',
        ],

        'filters' => [
            'name'         => 'Название',
            'company-name' => 'Компания Название',
            'created-by'   => 'Создано От',
            'start-date'   => 'Начать Дата',
            'end-date'     => 'Конец Дата',
            'created-at'   => 'Создано',
            'updated-at'   => 'Обновлено',
        ],

        'groups' => [
            'name'         => 'Название',
            'company-name' => 'Компания Название',
            'created-by'   => 'Создано От',
            'start-date'   => 'Начать Дата',
            'end-date'     => 'Конец Дата',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Mandatory день обновлено',
                    'body'  => 'mandatory день есть restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Mandatory день удалено',
                    'body'  => 'mandatory день есть удалено successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Mandatory дни удалено',
                    'body'  => 'mandatory дни есть удалено successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'name'       => 'Название',
            'start-date' => 'Начать Дата',
            'end-date'   => 'Конец Дата',
            'color'      => 'Цвет',
        ],
    ],
];
