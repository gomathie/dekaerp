<?php

return [
    'navigation' => [
        'title' => 'Banks',
        'group' => 'Банковский счётs',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Общее',

                'fields' => [
                    'name'  => 'Название',
                    'code'  => 'Банк Identifier Код',
                    'email' => 'Эл. почта',
                    'phone' => 'Телефон',
                ],
            ],

            'address' => [
                'title' => 'Адрес',

                'fields' => [
                    'address' => 'Адрес',
                    'city'    => 'Город',
                    'street1' => 'Street 1',
                    'street2' => 'Street 2',
                    'state'   => 'Состояние',
                    'zip'     => 'Zip',
                    'country' => 'Страна',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'           => 'Название',
            'code'           => 'Банк Identifier Код',
            'country'        => 'Страна',
            'created-at'     => 'Создано',
            'updated-at'     => 'Обновлено',
            'deleted-at'     => 'Удалено',
        ],

        'groups' => [
            'country'               => 'Страна',
            'created-at'            => 'Создано',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Банк обновлено',
                    'body'  => 'банк есть обновлено successfully.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Банк restored',
                    'body'  => 'банк есть restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Банк удалено',
                    'body'  => 'банк есть удалено successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Банк force удалено',
                    'body'  => 'банк есть force удалено successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Banks restored',
                    'body'  => 'banks есть restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Banks удалено',
                    'body'  => 'banks есть удалено successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Banks force удалено',
                    'body'  => 'banks есть force удалено successfully.',
                ],
            ],
        ],
    ],
];
