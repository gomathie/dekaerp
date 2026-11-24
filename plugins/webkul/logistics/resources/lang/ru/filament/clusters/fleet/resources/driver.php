<?php

return [
    'navigation' => [
        'title' => 'Водители',
    ],

    'form' => [
        'fields' => [
            'company'            => 'Компания',
            'employee'           => 'Сотрудник',
            'partner'            => 'Контакт перевозчика',
            'name'               => 'Имя',
            'phone'              => 'Телефон',
            'license-number'     => 'Номер удостоверения',
            'license-class'      => 'Категория удостоверения',
            'license-expires-at' => 'Удостоверение действует до',
            'is-active'          => 'Активен',
        ],
    ],

    'table' => [
        'columns' => [
            'name'               => 'Имя',
            'phone'              => 'Телефон',
            'employee'           => 'Сотрудник',
            'partner'            => 'Контакт перевозчика',
            'license-number'     => 'Номер удостоверения',
            'license-expires-at' => 'Срок действия удостоверения',
            'company'            => 'Компания',
            'is-active'          => 'Активен',
        ],

        'filters' => [
            'license-attention' => 'Истёк или скоро истекает',
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => 'Водитель',
            'license' => 'Удостоверение',
        ],
    ],

    'actions' => [
        'add-drivers-from-employees' => [
            'label' => 'Добавить водителей из сотрудников',

            'fields' => [
                'employees' => 'Сотрудники',
            ],

            'notification' => [
                'title' => 'Импорт сотрудников завершён',
                'body'  => 'Создано: :created, пропущено: :skipped.',
            ],
        ],
    ],
];
