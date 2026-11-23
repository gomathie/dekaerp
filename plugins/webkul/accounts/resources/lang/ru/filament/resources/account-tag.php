<?php

return [
    'form' => [
        'fields' => [
            'color'         => 'Цвет',
            'country'       => 'Страна',
            'applicability' => 'Applicability',
            'name'          => 'Название',
            'status'        => 'Статус',
            'tax-negate'    => 'Налог Negate',
        ],
    ],

    'table' => [
        'columns' => [
            'color'         => 'Цвет',
            'country'       => 'Страна',
            'created-by'    => 'Создано От',
            'applicability' => 'Applicability',
            'name'          => 'Название',
            'status'        => 'Статус',
            'tax-negate'    => 'Налог Negate',
            'created-at'    => 'Создано',
            'updated-at'    => 'Обновлено',
            'deleted-at'    => 'Удалено',
        ],

        'filters' => [
            'bank'           => 'Банк',
            'account-holder' => 'Счёт Holder',
            'creator'        => 'Creator',
            'can-send-money' => 'Можно Send Money',
        ],

        'groups' => [
            'country'       => 'Страна',
            'created-by'    => 'Создано От',
            'applicability' => 'Applicability',
            'name'          => 'Название',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Счёт Метка обновлено',
                    'body'  => 'счёт Метка есть обновлено successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Счёт Метка удалено',
                    'body'  => 'счёт Метка есть удалено successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Счёт Метки удалено',
                    'body'  => 'счёт Метки есть удалено successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'color'         => 'Цвет',
            'country'       => 'Страна',
            'applicability' => 'Applicability',
            'name'          => 'Название',
            'status'        => 'Статус',
            'tax-negate'    => 'Налог Negate',
        ],
    ],
];
