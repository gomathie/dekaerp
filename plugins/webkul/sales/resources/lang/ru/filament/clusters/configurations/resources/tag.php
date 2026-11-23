<?php

return [
    'title' => 'Метка',

    'navigation' => [
        'title' => 'Метка',
        'group' => 'Заказ на продажуs',
    ],

    'form' => [
        'fields' => [
            'name'  => 'Название',
            'color' => 'Цвет',
        ],
    ],

    'table' => [
        'columns' => [
            'created-by' => 'Создано от',
            'name'       => 'Название',
            'color'      => 'Цвет',
        ],
        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Товар Метка обновлено',
                    'body'  => 'товар Метка есть обновлено successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Товар Метка удалено',
                    'body'  => 'товар Метка есть удалено successfully.',
                ],
            ],
        ],
        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Товар Метка удалено',
                    'body'  => 'товар Метка есть удалено successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'name'  => 'Название',
            'color' => 'Цвет',
        ],
    ],
];
