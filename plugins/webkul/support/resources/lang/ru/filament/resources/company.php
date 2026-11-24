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
                'title'  => 'Сведения о компании',
                'fields' => [
                    'name'                  => 'Название компании',
                    'registration-number'   => 'Регистрационный номер',
                    'company-id'            => 'Идентификатор компании',
                    'tax-id'                => 'Налоговый номер',
                    'tax-id-tooltip'        => 'Налоговый номер — уникальный идентификатор вашей компании.',
                    'website'               => 'Веб-сайт',
                ],
            ],

            'address-information' => [
                'title'  => 'Адрес',

                'fields' => [
                    'street1'        => 'Улица 1',
                    'street2'        => 'Улица 2',
                    'city'           => 'Город',
                    'zipcode'        => 'Почтовый индекс',
                    'country'        => 'Страна',
                    'currency-name'  => 'Название валюты',
                    'phone-code'     => 'Телефонный код',
                    'code'           => 'Код',
                    'country-name'   => 'Название страны',
                    'state-required' => 'Регион обязателен',
                    'zip-required'   => 'Индекс обязателен',
                    'create-country' => 'Создать страну',
                    'state'          => 'Регион',
                    'state-name'     => 'Название региона',
                    'state-code'     => 'Код региона',
                    'create-state'   => 'Создать регион',
                ],
            ],

            'additional-information' => [
                'title' => 'Дополнительные сведения',

                'fields' => [
                    'default-currency'        => 'Валюта по умолчанию',
                    'currency-name'           => 'Название валюты',
                    'currency-full-name'      => 'Полное название валюты',
                    'currency-symbol'         => 'Символ валюты',
                    'currency-iso-numeric'    => 'Цифровой код ISO',
                    'currency-decimal-places' => 'Знаков после запятой',
                    'currency-rounding'       => 'Округление валюты',
                    'currency-status'         => 'Статус валюты',
                    'company-foundation-date' => 'Дата основания компании',
                    'currency-create'         => 'Создать валюту',
                    'status'                  => 'Статус',
                ],
            ],

            'branding' => [
                'title'  => 'Брендинг',
                'fields' => [
                    'company-logo' => 'Логотип компании',
                    'color'        => 'Цвет',
                ],
            ],

            'contact-information' => [
                'title'  => 'Контактные данные',
                'fields' => [
                    'email'  => 'Адрес эл. почты',
                    'phone'  => 'Номер телефона',
                    'mobile' => 'Номер телефона',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'logo'                 => 'Логотип',
            'company-name'         => 'Название компании',
            'branches'             => 'Филиалы',
            'email'                => 'Эл. почта',
            'city'                 => 'Город',
            'country'              => 'Страна',
            'currency'             => 'Валюта',
            'status'               => 'Статус',
            'created-by'           => 'Создал',
            'created-at'           => 'Создано',
            'updated-at'           => 'Обновлено',
        ],

        'groups' => [
            'company-name' => 'Название компании',
            'city'         => 'Город',
            'country'      => 'Страна',
            'state'        => 'Регион',
            'email'        => 'Эл. почта',
            'phone'        => 'Телефон',
            'currency'     => 'Валюта',
            'created-by'   => 'Создал',
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
                    'title' => 'Компания изменена',
                    'body'  => 'Компания успешно изменена.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Компания удалена',
                    'body'  => 'Компания успешно удалена.',

                    'default-company' => [
                        'title' => 'Нельзя удалить компанию по умолчанию',
                        'body'  => 'Нельзя удалить компанию, которая сейчас выбрана вашей компанией по умолчанию.',
                    ],
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Компания восстановлена',
                    'body'  => 'Компания успешно восстановлена.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Компания удалена безвозвратно',
                        'body'  => 'Компания успешно удалена безвозвратно.',
                    ],

                    'error' => [
                        'title' => 'Не удалось удалить компанию безвозвратно',
                        'body'  => 'Компанию нельзя удалить безвозвратно, так как она связана с другими записями.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Компании восстановлены',
                    'body'  => 'Компании успешно восстановлены.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Компании удалены',
                    'body'  => 'Компании успешно удалены.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Компании удалены безвозвратно',
                    'body'  => 'Компании успешно удалены безвозвратно.',

                    'error' => [
                        'title' => 'Не удалось удалить компании безвозвратно',
                        'body'  => 'Компании нельзя удалить безвозвратно, так как они связаны с другими записями.',
                    ],
                ],
            ],
        ],

        'empty-state-actions' => [
            'create' => [
                'notification' => [
                    'title' => 'Компании созданы',
                    'body'  => 'Компании успешно созданы.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'company-information' => [
                'title'   => 'Сведения о компании',
                'entries' => [
                    'name'                  => 'Название компании',
                    'registration-number'   => 'Регистрационный номер',
                    'company-id'            => 'Идентификатор компании',
                    'tax-id'                => 'Налоговый номер',
                    'tax-id-tooltip'        => 'Налоговый номер — уникальный идентификатор вашей компании.',
                    'website'               => 'Веб-сайт',
                ],
            ],

            'address-information' => [
                'title'  => 'Адрес',

                'entries' => [
                    'street1'        => 'Улица 1',
                    'street2'        => 'Улица 2',
                    'city'           => 'Город',
                    'zipcode'        => 'Почтовый индекс',
                    'country'        => 'Страна',
                    'currency-name'  => 'Название валюты',
                    'phone-code'     => 'Телефонный код',
                    'code'           => 'Код',
                    'country-name'   => 'Название страны',
                    'state-required' => 'Регион обязателен',
                    'zip-required'   => 'Индекс обязателен',
                    'create-country' => 'Создать страну',
                    'state'          => 'Регион',
                    'state-name'     => 'Название региона',
                    'state-code'     => 'Код региона',
                    'create-state'   => 'Создать регион',
                ],
            ],

            'additional-information' => [
                'title' => 'Дополнительные сведения',

                'entries' => [
                    'default-currency'        => 'Валюта по умолчанию',
                    'currency-name'           => 'Название валюты',
                    'currency-full-name'      => 'Полное название валюты',
                    'currency-symbol'         => 'Символ валюты',
                    'currency-iso-numeric'    => 'Цифровой код ISO',
                    'currency-decimal-places' => 'Знаков после запятой',
                    'currency-rounding'       => 'Округление валюты',
                    'currency-status'         => 'Статус валюты',
                    'company-foundation-date' => 'Дата основания компании',
                    'currency-create'         => 'Создать валюту',
                    'status'                  => 'Статус',
                ],
            ],

            'branding' => [
                'title'   => 'Брендинг',
                'entries' => [
                    'company-logo' => 'Логотип компании',
                    'color'        => 'Цвет',
                ],
            ],

            'contact-information' => [
                'title'   => 'Контактные данные',
                'entries' => [
                    'email'  => 'Адрес эл. почты',
                    'phone'  => 'Номер телефона',
                    'mobile' => 'Номер телефона',
                ],
            ],
        ],
    ],
];
