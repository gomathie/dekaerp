<?php

return [
    'navigation' => [
        'title' => 'Банковский счётs',
        'group' => 'Banks',
    ],

    'form' => [
        'account-number'     => 'Счёт Номер',
        'bank'               => [
            'title'    => 'Банк',
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

        'account-holder'     => 'Счёт Holder',
    ],

    'table' => [
        'columns' => [
            'account-number' => 'Счёт Номер',
            'bank'           => 'Банк',
            'account-holder' => 'Счёт Holder',
            'send-money'     => 'Можно Send Money',
            'created-at'     => 'Создано',
            'updated-at'     => 'Обновлено',
            'deleted-at'     => 'Удалено',
        ],

        'filters' => [
            'bank'           => 'Банк',
            'account-holder' => 'Счёт Holder',
            'creator'        => 'Creator',
            'can-send-money' => 'Можно Send Money',
        ],

        'groups' => [
            'bank'               => 'Банк',
            'can-send-money'     => 'Можно Send Money',
            'created-at'         => 'Создано',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Банковский счёт обновлено',
                    'body'  => 'банковский счёт есть обновлено successfully.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Банковский счёт restored',
                    'body'  => 'банковский счёт есть restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Банковский счёт удалено',
                    'body'  => 'банковский счёт есть удалено successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Банковский счёт force удалено',
                    'body'  => 'банковский счёт есть force удалено successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Банковский счётs restored',
                    'body'  => 'банковский счётs есть restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Банковский счётs удалено',
                    'body'  => 'банковский счётs есть удалено successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Банковский счётs force удалено',
                    'body'  => 'банковский счётs есть force удалено successfully.',
                ],
            ],
        ],
    ],
];
