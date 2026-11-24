<?php

return [
    'form' => [
        'tabs' => [
            'general-information' => [
                'title' => 'Общие сведения',

                'sections' => [
                    'branch-information' => [
                        'title' => 'Сведения о филиале',

                        'fields' => [
                            'company-name'                => 'Название компании',
                            'registration-number'         => 'Регистрационный номер',
                            'tax-id'                      => 'Налоговый номер',
                            'tax-id-tooltip'              => 'Налоговый номер — уникальный идентификатор вашей компании.',
                            'color'                       => 'Цвет',
                            'company-id'                  => 'Идентификатор компании',
                            'company-id-tooltip'          => 'Идентификатор компании — уникальный идентификатор вашей компании.',
                        ],
                    ],

                    'branding' => [
                        'title'  => 'Брендинг',
                        'fields' => [
                            'branch-logo' => 'Логотип филиала',
                        ],
                    ],
                ],
            ],

            'address-information' => [
                'title' => 'Адрес',

                'sections' => [
                    'address-information' => [
                        'title' => 'Адрес',

                        'fields' => [
                            'street1'                => 'Улица 1',
                            'street2'                => 'Улица 2',
                            'city'                   => 'Город',
                            'zip'                    => 'Почтовый индекс',
                            'country'                => 'Страна',
                            'country-currency-name'  => 'Название валюты',
                            'country-phone-code'     => 'Телефонный код',
                            'country-code'           => 'Код',
                            'country-name'           => 'Название страны',
                            'country-state-required' => 'Регион обязателен',
                            'country-zip-required'   => 'Индекс обязателен',
                            'country-create'         => 'Создать страну',
                            'state'                  => 'Регион',
                            'state-name'             => 'Название региона',
                            'state-code'             => 'Код региона',
                            'zip-code'               => 'Почтовый индекс',
                            'state-create'           => 'Создать регион',
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
                            'currency-create'         => 'Создать валюту',
                            'company-foundation-date' => 'Дата основания компании',
                            'status'                  => 'Статус',
                        ],
                    ],
                ],
            ],

            'contact-information' => [
                'title' => 'Контактные данные',

                'sections' => [
                    'contact-information' => [
                        'title' => 'Контактные данные',

                        'fields' => [
                            'email-address' => 'Адрес эл. почты',
                            'phone-number'  => 'Номер телефона',
                            'mobile-number' => 'Номер телефона',
                        ],
                    ],
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'logo'                 => 'Логотип',
            'company-name'         => 'Название филиала',
            'branches'             => 'Филиалы',
            'email'                => 'Эл. почта',
            'city'                 => 'Город',
            'country'              => 'Страна',
            'currency'             => 'Валюта',
            'status'               => 'Статус',
            'created-at'           => 'Создано',
            'updated-at'           => 'Обновлено',
        ],

        'groups' => [
            'company-name' => 'Название филиала',
            'city'         => 'Город',
            'country'      => 'Страна',
            'state'        => 'Регион',
            'email'        => 'Эл. почта',
            'phone'        => 'Телефон',
            'currency'     => 'Валюта',
            'created-at'   => 'Создано',
            'updated-at'   => 'Обновлено',
        ],

        'filters' => [
            'trashed' => 'Удалённые',
            'status'  => 'Статус',
            'country' => 'Страна',
        ],

        'header-actions' => [
            'create' => [
                'notification' => [
                    'title' => 'Филиал создан',
                    'body'  => 'Филиал успешно создан.',
                ],
            ],
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Филиал обновлён',
                    'body'  => 'Филиал успешно обновлён.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Филиал удалён',
                    'body'  => 'Филиал успешно удалён.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Филиал восстановлен',
                    'body'  => 'Филиал успешно восстановлен.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Филиалы восстановлены',
                    'body'  => 'Филиалы успешно восстановлены.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Филиалы удалены',
                    'body'  => 'Филиалы успешно удалены.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Филиалы удалены безвозвратно',
                    'body'  => 'Филиалы успешно удалены безвозвратно.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'tabs' => [
            'general-information' => [
                'title' => 'Общие сведения',

                'sections' => [
                    'branch-information' => [
                        'title' => 'Сведения о филиале',

                        'entries' => [
                            'company-name'                => 'Название компании',
                            'registration-number'         => 'Регистрационный номер',
                            'tax-id'                      => 'Налоговый номер',
                            'registration-number-tooltip' => 'Налоговый номер — уникальный идентификатор вашей компании.',
                            'color'                       => 'Цвет',
                        ],
                    ],

                    'branding' => [
                        'title'   => 'Брендинг',
                        'entries' => [
                            'branch-logo' => 'Логотип филиала',
                        ],
                    ],
                ],
            ],

            'address-information' => [
                'title' => 'Адрес',

                'sections' => [
                    'address-information' => [
                        'title' => 'Адрес',

                        'entries' => [
                            'street1'                => 'Улица 1',
                            'street2'                => 'Улица 2',
                            'city'                   => 'Город',
                            'zip'                    => 'Почтовый индекс',
                            'country'                => 'Страна',
                            'country-currency-name'  => 'Название валюты',
                            'country-phone-code'     => 'Телефонный код',
                            'country-code'           => 'Код',
                            'country-name'           => 'Название страны',
                            'country-state-required' => 'Регион обязателен',
                            'country-zip-required'   => 'Индекс обязателен',
                            'country-create'         => 'Создать страну',
                            'state'                  => 'Регион',
                            'state-name'             => 'Название региона',
                            'state-code'             => 'Код региона',
                            'zip-code'               => 'Почтовый индекс',
                            'state-create'           => 'Создать регион',
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
                            'currency-create'         => 'Создать валюту',
                            'company-foundation-date' => 'Дата основания компании',
                            'status'                  => 'Статус',
                        ],
                    ],
                ],
            ],

            'contact-information' => [
                'title' => 'Контактные данные',

                'sections' => [
                    'contact-information' => [
                        'title' => 'Контактные данные',

                        'entries' => [
                            'email-address' => 'Адрес эл. почты',
                            'phone-number'  => 'Номер телефона',
                            'mobile-number' => 'Номер телефона',
                        ],
                    ],
                ],
            ],
        ],
    ],
];
