<?php

return [
    'form' => [
        'fields' => [
            'color'         => 'Цвет',
            'country'       => 'Страна',
            'applicability' => 'Применимость',
            'name'          => 'Название',
            'status'        => 'Статус',
            'tax-negate'    => 'Инвертировать налог',
        ],
    ],

    'table' => [
        'columns' => [
            'color'         => 'Цвет',
            'country'       => 'Страна',
            'created-by'    => 'Создал',
            'applicability' => 'Применимость',
            'name'          => 'Название',
            'status'        => 'Статус',
            'tax-negate'    => 'Инвертировать налог',
            'created-at'    => 'Создано',
            'updated-at'    => 'Обновлено',
            'deleted-at'    => 'Удалено',
        ],

        'filters' => [
            'bank'           => 'Банк',
            'account-holder' => 'Владелец счёта',
            'creator'        => 'Автор',
            'can-send-money' => 'Может отправлять деньги',
        ],

        'groups' => [
            'country'       => 'Страна',
            'created-by'    => 'Создал',
            'applicability' => 'Применимость',
            'name'          => 'Название',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Тег счёта обновлён',
                    'body'  => 'Тег счёта успешно обновлён.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Тег счёта удалён',
                    'body'  => 'Тег счёта успешно удалён.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Теги счетов удалены',
                    'body'  => 'Теги счетов успешно удалены.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'color'         => 'Цвет',
            'country'       => 'Страна',
            'applicability' => 'Применимость',
            'name'          => 'Название',
            'status'        => 'Статус',
            'tax-negate'    => 'Инвертировать налог',
        ],
    ],
];
