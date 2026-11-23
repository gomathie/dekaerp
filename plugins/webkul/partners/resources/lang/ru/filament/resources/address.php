<?php

return [
    'form' => [
        'partner' => 'Контрагент',
        'name'    => 'Название',
        'email'   => 'Эл. почта',
        'phone'   => 'Телефон',
        'mobile'  => 'Mobile',
        'type'    => 'Тип',
        'address' => 'Адрес',
        'city'    => 'Город',
        'street1' => 'Street 1',
        'street2' => 'Street 2',
        'state'   => 'Состояние',
        'zip'     => 'Zip',
        'code'    => 'Код',
        'country' => 'Страна',
    ],

    'table' => [
        'header-actions' => [
            'create' => [
                'label' => 'Добавить Адрес',

                'notification' => [
                    'title' => 'Адрес создано',
                    'body'  => 'адрес есть создано successfully.',
                ],
            ],
        ],

        'columns' => [
            'type'    => 'Тип',
            'name'    => 'Контакт Название',
            'address' => 'Адрес',
            'city'    => 'Город',
            'street1' => 'Street 1',
            'street2' => 'Street 2',
            'state'   => 'Состояние',
            'zip'     => 'Zip',
            'country' => 'Страна',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Адрес обновлено',
                    'body'  => 'адрес есть обновлено successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Адрес удалено',
                    'body'  => 'адрес есть удалено successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Addresses удалено',
                    'body'  => 'addresses есть удалено successfully.',
                ],
            ],
        ],
    ],
];
