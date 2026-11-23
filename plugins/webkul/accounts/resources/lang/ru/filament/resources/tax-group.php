<?php

return [
    'form' => [
        'sections' => [
            'fields' => [
                'company'            => 'Компания',
                'country'            => 'Страна',
                'name'               => 'Название',
                'preceding-subtotal' => 'Preceding Промежуточный итог',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'company'            => 'Компания',
            'country'            => 'Страна',
            'created-by'         => 'Создано От',
            'name'               => 'Название',
            'preceding-subtotal' => 'Preceding Промежуточный итог',
            'created-at'         => 'Создано',
            'updated-at'         => 'Обновлено',
        ],

        'groups' => [
            'name'       => 'Название',
            'company'    => 'Компания',
            'country'    => 'Страна',
            'created-by' => 'Создано От',
            'created-at' => 'Создано',
            'updated-at' => 'Обновлено',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Налог Группа удалено',
                        'body'  => 'налог группа есть удалено successfully.',
                    ],

                    'error' => [
                        'title' => 'Налог Группа could не быть удалено',
                        'body'  => 'налог группа нельзя быть удалено потому что это currently в использовать.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Налог Groups удалено',
                        'body'  => 'налог groups есть удалено successfully.',
                    ],

                    'error' => [
                        'title' => 'Налог Groups could не быть удалено',
                        'body'  => 'налог groups нельзя быть удалено потому что они currently в использовать.',
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
                'preceding-subtotal' => 'Preceding Промежуточный итог',
            ],
        ],
    ],
];
