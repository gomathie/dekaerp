<?php

return [
    'global-search' => [
        'email' => 'Эл. почта',
        'phone' => 'Телефон',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Общее',

                'fields' => [
                    'company'    => 'Компания',
                    'avatar'     => 'Avatar',
                    'tax-id'     => 'Налог Ид.',
                    'job-title'  => 'Должность Заголовок',
                    'phone'      => 'Телефон',
                    'mobile'     => 'Mobile',
                    'email'      => 'Эл. почта',
                    'website'    => 'Сайт',
                    'title'      => 'Заголовок',
                    'name'       => 'Название',
                    'short-name' => 'Short Название',
                    'tags'       => 'Метки',
                    'color'      => 'Цвет',
                ],

                'address' => [
                    'title' => 'Адрес',

                    'fields' => [
                        'street1'  => 'Street 1',
                        'street2'  => 'Street 2',
                        'city'     => 'Город',
                        'zip'      => 'ZIP',
                        'state'    => 'Состояние',
                        'country'  => 'Страна',
                        'name'     => 'Название',
                        'code'     => 'Код',
                    ],
                ],
            ],
        ],

        'tabs' => [
            'sales-purchase' => [
                'title' => 'Продажи и Закупки',

                'fields' => [
                    'responsible'           => 'Responsible',
                    'responsible-hint-text' => 'Этот внутренний salesperson responsible для этот клиент',
                    'company-id'            => 'Компания Ид.',
                    'company-id-hint-text'  => 'company\'s registry номер, используется если different от Налог Ид.. Это нужно быть unique among все контрагенты в пределах тот же страна.',
                    'reference'             => 'Ссылка',
                    'industry'              => 'Industry',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'parent'     => 'Parent',
            'work-email' => 'Work Эл. почта',
            'work-phone' => 'Work Телефон',
        ],

        'groups' => [
            'account-type' => 'Счёт Тип',
            'parent'       => 'Parent',
            'title'        => 'Заголовок',
            'job-title'    => 'Должность Заголовок',
            'industry'     => 'Industry',
        ],

        'filters' => [
            'account-type'     => 'Счёт Тип',
            'name'             => 'Название',
            'email'            => 'Эл. почта',
            'parent'           => 'Parent',
            'title'            => 'Заголовок',
            'tax-id'           => 'Налог Ид.',
            'phone'            => 'Телефон',
            'mobile'           => 'Mobile',
            'job-title'        => 'Должность Заголовок',
            'website'          => 'Сайт',
            'company-registry' => 'Компания Registry',
            'responsible'      => 'Responsible',
            'reference'        => 'Ссылка',
            'parent'           => 'Parent',
            'creator'          => 'Creator',
            'company'          => 'Компания',
            'industry'         => 'Industry',
            'industry'         => 'Industry',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Контакт обновлено',
                    'body'  => 'контакт есть обновлено successfully.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Контакт restored',
                    'body'  => 'контакт есть restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Контакт удалено',
                    'body'  => 'контакт есть удалено successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Контакт force удалено',
                        'body'  => 'контакт есть force удалено successfully.',
                    ],

                    'error' => [
                        'title' => 'Контакт could не быть удалено',
                        'body'  => 'контакт нельзя быть удалено потому что это currently в использовать.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Контакты restored',
                    'body'  => 'контакты есть restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Контакты удалено',
                    'body'  => 'контакты есть удалено successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Контакты force удалено',
                        'body'  => 'контакты есть force удалено successfully.',
                    ],

                    'error' => [
                        'title' => 'Контакты could не быть удалено',
                        'body'  => 'контакты нельзя быть удалено потому что они currently в использовать.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Общее',

                'fields' => [
                    'company'    => 'Компания',
                    'avatar'     => 'Avatar',
                    'tax-id'     => 'Налог Ид.',
                    'job-title'  => 'Должность Заголовок',
                    'phone'      => 'Телефон',
                    'mobile'     => 'Mobile',
                    'email'      => 'Эл. почта',
                    'website'    => 'Сайт',
                    'title'      => 'Заголовок',
                    'name'       => 'Название',
                    'short-name' => 'Short Название',
                    'tags'       => 'Метки',
                ],

                'address' => [
                    'title' => 'Адрес',

                    'fields' => [
                        'street1'  => 'Street 1',
                        'street2'  => 'Street 2',
                        'city'     => 'Город',
                        'zip'      => 'ZIP',
                        'state'    => 'Состояние',
                        'country'  => 'Страна',
                        'name'     => 'Название',
                        'code'     => 'Код',
                    ],
                ],
            ],
        ],

        'tabs' => [
            'sales-purchase' => [
                'title' => 'Продажи и Закупки',

                'fields' => [
                    'responsible'           => 'Responsible',
                    'responsible-hint-text' => 'Этот внутренний salesperson responsible для этот клиент',
                    'company-id'            => 'Компания Ид.',
                    'company-id-hint-text'  => 'registry номер компания. Использовать это если это different от Налог Ид.. Это нужно быть unique по все контрагенты тот же страна',
                    'reference'             => 'Ссылка',
                    'industry'              => 'Industry',
                ],
            ],
        ],
    ],
];
