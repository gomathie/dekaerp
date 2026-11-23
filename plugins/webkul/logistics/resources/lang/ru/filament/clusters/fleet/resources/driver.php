<?php

return [
    'navigation' => [
        'title' => 'Водители',
    ],

    'form' => [
        'fields' => [
            'company'            => 'Компания',
            'employee'           => 'Сотрудник',
            'partner'            => 'Carrier контакт',
            'name'               => 'Название',
            'phone'              => 'Телефон',
            'license-number'     => 'Licence номер',
            'license-class'      => 'Licence class',
            'license-expires-at' => 'Licence expires',
            'is-active'          => 'Активен',
        ],
    ],

    'table' => [
        'columns' => [
            'name'               => 'Название',
            'phone'              => 'Телефон',
            'employee'           => 'Сотрудник',
            'partner'            => 'Carrier контакт',
            'license-number'     => 'Licence номер',
            'license-expires-at' => 'Licence expiry',
            'company'            => 'Компания',
            'is-active'          => 'Активен',
        ],

        'filters' => [
            'license-attention' => 'Expired или expiring скоро',
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => 'Водитель',
            'license' => 'Licence',
        ],
    ],

    'actions' => [
        'add-drivers-from-employees' => [
            'label' => 'Добавить водители от сотрудники',

            'fields' => [
                'employees' => 'Сотрудники',
            ],

            'notification' => [
                'title' => 'Сотрудник импорт finished',
                'body'  => ':created создано, :skipped skipped.',
            ],
        ],
    ],
];
