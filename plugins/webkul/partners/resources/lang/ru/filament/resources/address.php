<?php

return [
    'form' => [
        'partner' => 'Контакт',
        'name'    => 'Имя',
        'email'   => 'Эл. почта',
        'phone'   => 'Телефон',
        'mobile'  => 'Мобильный',
        'type'    => 'Тип',
        'address' => 'Адрес',
        'city'    => 'Город',
        'street1' => 'Улица 1',
        'street2' => 'Улица 2',
        'state'   => 'Регион',
        'zip'     => 'Индекс',
        'code'    => 'Код',
        'country' => 'Страна',
    ],

    'table' => [
        'header-actions' => [
            'create' => [
                'label' => 'Добавить адрес',

                'notification' => [
                    'title' => 'Адрес создан',
                    'body'  => 'Адрес успешно создан.',
                ],
            ],
        ],

        'columns' => [
            'type'    => 'Тип',
            'name'    => 'Контактное лицо',
            'address' => 'Адрес',
            'city'    => 'Город',
            'street1' => 'Улица 1',
            'street2' => 'Улица 2',
            'state'   => 'Регион',
            'zip'     => 'Индекс',
            'country' => 'Страна',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Адрес обновлён',
                    'body'  => 'Адрес успешно обновлён.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Адрес удалён',
                    'body'  => 'Адрес успешно удалён.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Адреса удалены',
                    'body'  => 'Адреса успешно удалены.',
                ],
            ],
        ],
    ],
];
