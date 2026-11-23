<?php

return [
    'title' => 'Компании',

    'navigation' => [
        'title' => 'Компании',
    ],

    'global-search' => [
        'email' => 'Эл. почта',
    ],

    'form' => [
        'sections' => [
            'company-information' => [
                'title'  => 'Компания Сведения',
                'fields' => [
                    'name'                  => 'Компания Название',
                    'registration-number'   => 'Registration Номер',
                    'company-id'            => 'Компания Ид.',
                    'tax-id'                => 'Налог Ид.',
                    'tax-id-tooltip'        => 'Налог Ид. unique identifier для ваш компания.',
                    'website'               => 'Сайт',
                ],
            ],

            'address-information' => [
                'title'  => 'Адрес Сведения',

                'fields' => [
                    'street1'        => 'Street 1',
                    'street2'        => 'Street 2',
                    'city'           => 'Город',
                    'zipcode'        => 'Zip Код',
                    'country'        => 'Страна',
                    'currency-name'  => 'Валюта Название',
                    'phone-code'     => 'Телефон Код',
                    'code'           => 'Код',
                    'country-name'   => 'Страна Название',
                    'state-required' => 'Состояние Обязательно',
                    'zip-required'   => 'Zip Обязательно',
                    'create-country' => 'Создать Страна',
                    'state'          => 'Состояние',
                    'state-name'     => 'Состояние Название',
                    'state-code'     => 'Состояние Код',
                    'create-state'   => 'Создать Состояние',
                ],
            ],

            'additional-information' => [
                'title' => 'Additional Сведения',

                'fields' => [
                    'default-currency'        => 'По умолчанию Валюта',
                    'currency-name'           => 'Валюта Название',
                    'currency-full-name'      => 'Валюта Полный Название',
                    'currency-symbol'         => 'Валюта Symbol',
                    'currency-iso-numeric'    => 'Валюта ISO Numeric',
                    'currency-decimal-places' => 'Валюта Decimal Places',
                    'currency-rounding'       => 'Валюта Rounding',
                    'currency-status'         => 'Валюта Статус',
                    'company-foundation-date' => 'Компания Foundation Дата',
                    'currency-create'         => 'Создать Валюта',
                    'status'                  => 'Статус',
                ],
            ],

            'branding' => [
                'title'  => 'Branding',
                'fields' => [
                    'company-logo' => 'Компания Логотип',
                    'color'        => 'Цвет',
                ],
            ],

            'contact-information' => [
                'title'  => 'Контакт Сведения',
                'fields' => [
                    'email'  => 'Эл. почта Адрес',
                    'phone'  => 'Телефон Номер',
                    'mobile' => 'Телефон Номер',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'logo'                 => 'Логотип',
            'company-name'         => 'Компания Название',
            'branches'             => 'Branches',
            'email'                => 'Эл. почта',
            'city'                 => 'Город',
            'country'              => 'Страна',
            'currency'             => 'Валюта',
            'status'               => 'Статус',
            'created-by'           => 'Создано От',
            'created-at'           => 'Создано',
            'updated-at'           => 'Обновлено',
        ],

        'groups' => [
            'company-name' => 'Компания Название',
            'city'         => 'Город',
            'country'      => 'Страна',
            'state'        => 'Состояние',
            'email'        => 'Эл. почта',
            'phone'        => 'Телефон',
            'currency'     => 'Валюта',
            'created-by'   => 'Создано От',
            'created-at'   => 'Создано',
            'updated-at'   => 'Обновлено',
        ],

        'filters' => [
            'status'  => 'Статус',
            'country' => 'Страна',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Компания edited',
                    'body'  => 'компания есть edited successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Компания удалено',
                    'body'  => 'компания есть удалено successfully.',

                    'default-company' => [
                        'title' => 'Нельзя удалить по умолчанию компания',
                        'body'  => 'Вы нельзя удалить компания currently задать как ваш по умолчанию компания.',
                    ],
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Компания restored',
                    'body'  => 'компания есть restored successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Компания force удалено',
                        'body'  => 'компания есть force удалено successfully.',
                    ],

                    'error' => [
                        'title' => 'Компания force deletion не удалось',
                        'body'  => 'компания could не быть force удалено потому что это associated с другое записи.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Компании restored',
                    'body'  => 'компании есть restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Компании удалено',
                    'body'  => 'компании есть удалено successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Компании force удалено',
                    'body'  => 'компании есть force удалено successfully.',

                    'error' => [
                        'title' => 'Компании force deletion не удалось',
                        'body'  => 'компании could не быть force удалено потому что они associated с другое записи.',
                    ],
                ],
            ],
        ],

        'empty-state-actions' => [
            'create' => [
                'notification' => [
                    'title' => 'Компании создано',
                    'body'  => 'компании есть создано successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'company-information' => [
                'title'   => 'Компания Сведения',
                'entries' => [
                    'name'                  => 'Компания Название',
                    'registration-number'   => 'Registration Номер',
                    'company-id'            => 'Компания Ид.',
                    'tax-id'                => 'Налог Ид.',
                    'tax-id-tooltip'        => 'Налог Ид. unique identifier для ваш компания.',
                    'website'               => 'Сайт',
                ],
            ],

            'address-information' => [
                'title'  => 'Адрес Сведения',

                'entries' => [
                    'street1'        => 'Street 1',
                    'street2'        => 'Street 2',
                    'city'           => 'Город',
                    'zipcode'        => 'Zip Код',
                    'country'        => 'Страна',
                    'currency-name'  => 'Валюта Название',
                    'phone-code'     => 'Телефон Код',
                    'code'           => 'Код',
                    'country-name'   => 'Страна Название',
                    'state-required' => 'Состояние Обязательно',
                    'zip-required'   => 'Zip Обязательно',
                    'create-country' => 'Создать Страна',
                    'state'          => 'Состояние',
                    'state-name'     => 'Состояние Название',
                    'state-code'     => 'Состояние Код',
                    'create-state'   => 'Создать Состояние',
                ],
            ],

            'additional-information' => [
                'title' => 'Additional Сведения',

                'entries' => [
                    'default-currency'        => 'По умолчанию Валюта',
                    'currency-name'           => 'Валюта Название',
                    'currency-full-name'      => 'Валюта Полный Название',
                    'currency-symbol'         => 'Валюта Symbol',
                    'currency-iso-numeric'    => 'Валюта ISO Numeric',
                    'currency-decimal-places' => 'Валюта Decimal Places',
                    'currency-rounding'       => 'Валюта Rounding',
                    'currency-status'         => 'Валюта Статус',
                    'company-foundation-date' => 'Компания Foundation Дата',
                    'currency-create'         => 'Создать Валюта',
                    'status'                  => 'Статус',
                ],
            ],

            'branding' => [
                'title'   => 'Branding',
                'entries' => [
                    'company-logo' => 'Компания Логотип',
                    'color'        => 'Цвет',
                ],
            ],

            'contact-information' => [
                'title'   => 'Контакт Сведения',
                'entries' => [
                    'email'  => 'Эл. почта Адрес',
                    'phone'  => 'Телефон Номер',
                    'mobile' => 'Телефон Номер',
                ],
            ],
        ],
    ],
];
