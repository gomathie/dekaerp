<?php

return [
    'title' => 'Публичный Holidays',

    'model-label' => 'Публичный holiday',

    'navigation' => [
        'title' => 'Публичный Holidays',
    ],

    'form' => [
        'fields' => [
            'name'             => 'Название',
            'name-placeholder' => 'Введите название публичный holiday',
            'date-from'        => 'Начать Дата',
            'date-to'          => 'Конец Дата',
            'color'            => 'Цвет',
            'calendar'         => 'Календарь',
        ],
    ],

    'table' => [
        'columns' => [
            'name'         => 'Название',
            'company-name' => 'Компания Название',
            'calendar'     => 'Календарь',
            'created-by'   => 'Создано От',
            'date-from'    => 'Начать Дата',
            'date-to'      => 'Конец Дата',
        ],

        'filters' => [
            'name'         => 'Название',
            'company-name' => 'Компания Название',
            'created-by'   => 'Создано От',
            'date-from'    => 'Начать Дата',
            'date-to'      => 'Конец Дата',
            'created-at'   => 'Создано',
            'updated-at'   => 'Обновлено',
        ],

        'groups' => [
            'name'         => 'Название',
            'company-name' => 'Компания Название',
            'created-by'   => 'Создано От',
            'date-from'    => 'Начать Дата',
            'date-to'      => 'Конец Дата',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Публичный holiday обновлено',
                    'body'  => 'публичный holiday есть restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Публичный holiday удалено',
                    'body'  => 'публичный holiday есть удалено successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Публичный holidays удалено',
                    'body'  => 'публичный holidays есть удалено successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'name'      => 'Название',
            'date-from' => 'Начать Дата',
            'date-to'   => 'Конец Дата',
            'color'     => 'Цвет',
        ],
    ],
];
