<?php

return [
    'navigation' => [
        'title' => 'Банки',
        'group' => 'Банковские счета',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Общие',

                'fields' => [
                    'name'  => 'Название',
                    'code'  => 'Код банка (BIC)',
                    'email' => 'Эл. почта',
                    'phone' => 'Телефон',
                ],
            ],

            'address' => [
                'title' => 'Адрес',

                'fields' => [
                    'address' => 'Адрес',
                    'city'    => 'Город',
                    'street1' => 'Улица 1',
                    'street2' => 'Улица 2',
                    'state'   => 'Регион',
                    'zip'     => 'Индекс',
                    'country' => 'Страна',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'           => 'Название',
            'code'           => 'Код банка (BIC)',
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
                    'title' => 'Банк обновлён',
                    'body'  => 'Банк успешно обновлён.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Банк восстановлен',
                    'body'  => 'Банк успешно восстановлен.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Банк удалён',
                    'body'  => 'Банк успешно удалён.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Банк удалён безвозвратно',
                    'body'  => 'Банк успешно удалён безвозвратно.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Банки восстановлены',
                    'body'  => 'Банки успешно восстановлены.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Банки удалены',
                    'body'  => 'Банки успешно удалены.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Банки удалены безвозвратно',
                    'body'  => 'Банки успешно удалены безвозвратно.',
                ],
            ],
        ],
    ],
];
