<?php

return [
    'navigation' => [
        'title' => 'Банковские счета',
        'group' => 'Банки',
    ],

    'form' => [
        'account-number'     => 'Номер счёта',
        'bank'               => [
            'title'    => 'Банк',
            'sections' => [
                'general' => [
                    'title' => 'Общее',

                    'fields' => [
                        'name'  => 'Название',
                        'code'  => 'БИК / SWIFT',
                        'email' => 'Эл. почта',
                        'phone' => 'Телефон',
                    ],
                ],

                'address' => [
                    'title' => 'Адрес',

                    'fields' => [
                        'address' => 'Адрес',
                        'city'    => 'Город',
                        'street1' => 'Улица, строка 1',
                        'street2' => 'Улица, строка 2',
                        'state'   => 'Регион',
                        'zip'     => 'Индекс',
                        'country' => 'Страна',
                    ],
                ],
            ],
        ],

        'account-holder'     => 'Владелец счёта',
    ],

    'table' => [
        'columns' => [
            'account-number' => 'Номер счёта',
            'bank'           => 'Банк',
            'account-holder' => 'Владелец счёта',
            'send-money'     => 'Может отправлять деньги',
            'created-at'     => 'Создано',
            'updated-at'     => 'Обновлено',
            'deleted-at'     => 'Удалено',
        ],

        'filters' => [
            'bank'           => 'Банк',
            'account-holder' => 'Владелец счёта',
            'creator'        => 'Автор',
            'can-send-money' => 'Может отправлять деньги',
        ],

        'groups' => [
            'bank'               => 'Банк',
            'can-send-money'     => 'Может отправлять деньги',
            'created-at'         => 'Создано',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Банковский счёт обновлён',
                    'body'  => 'Банковский счёт успешно обновлён.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Банковский счёт восстановлен',
                    'body'  => 'Банковский счёт успешно восстановлен.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Банковский счёт удалён',
                    'body'  => 'Банковский счёт успешно удалён.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Банковский счёт удалён навсегда',
                    'body'  => 'Банковский счёт успешно удалён навсегда.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Банковские счета восстановлены',
                    'body'  => 'Банковские счета успешно восстановлены.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Банковские счета удалены',
                    'body'  => 'Банковские счета успешно удалены.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Банковские счета удалены навсегда',
                    'body'  => 'Банковские счета успешно удалены навсегда.',
                ],
            ],
        ],
    ],
];
