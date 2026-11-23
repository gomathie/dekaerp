<?php

return [
    'form' => [
        'tabs' => [
            'general-information' => [
                'title' => 'Общее Сведения',

                'sections' => [
                    'branch-information' => [
                        'title' => 'Branch Сведения',

                        'fields' => [
                            'company-name'                => 'Компания Название',
                            'registration-number'         => 'Registration Номер',
                            'tax-id'                      => 'Налог Ид.',
                            'tax-id-tooltip'              => 'Налог Ид. unique identifier для ваш компания.',
                            'color'                       => 'Цвет',
                            'company-id'                  => 'Компания Ид.',
                            'company-id-tooltip'          => 'Компания Ид. unique identifier для ваш компания.',
                        ],
                    ],

                    'branding' => [
                        'title'  => 'Branding',
                        'fields' => [
                            'branch-logo' => 'Branch Логотип',
                        ],
                    ],
                ],
            ],

            'address-information' => [
                'title' => 'Адрес Сведения',

                'sections' => [
                    'address-information' => [
                        'title' => 'Адрес Сведения',

                        'fields' => [
                            'street1'                => 'Street 1',
                            'street2'                => 'Street 2',
                            'city'                   => 'Город',
                            'zip'                    => 'Zip Код',
                            'country'                => 'Страна',
                            'country-currency-name'  => 'Валюта Название',
                            'country-phone-code'     => 'Телефон Код',
                            'country-code'           => 'Код',
                            'country-name'           => 'Страна Название',
                            'country-state-required' => 'Состояние Обязательно',
                            'country-zip-required'   => 'Zip Обязательно',
                            'country-create'         => 'Создать Страна',
                            'state'                  => 'Состояние',
                            'state-name'             => 'Состояние Название',
                            'state-code'             => 'Состояние Код',
                            'zip-code'               => 'Zip Код',
                            'state-create'           => 'Создать Состояние',
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
                            'currency-create'         => 'Создать Валюта',
                            'company-foundation-date' => 'Компания Foundation Дата',
                            'status'                  => 'Статус',
                        ],
                    ],
                ],
            ],

            'contact-information' => [
                'title' => 'Контакт Сведения',

                'sections' => [
                    'contact-information' => [
                        'title' => 'Контакт Сведения',

                        'fields' => [
                            'email-address' => 'Эл. почта Адрес',
                            'phone-number'  => 'Телефон Номер',
                            'mobile-number' => 'Телефон Номер',
                        ],
                    ],
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'logo'                 => 'Логотип',
            'company-name'         => 'Branch Название',
            'branches'             => 'Branches',
            'email'                => 'Эл. почта',
            'city'                 => 'Город',
            'country'              => 'Страна',
            'currency'             => 'Валюта',
            'status'               => 'Статус',
            'created-at'           => 'Создано',
            'updated-at'           => 'Обновлено',
        ],

        'groups' => [
            'company-name' => 'Branch Название',
            'city'         => 'Город',
            'country'      => 'Страна',
            'state'        => 'Состояние',
            'email'        => 'Эл. почта',
            'phone'        => 'Телефон',
            'currency'     => 'Валюта',
            'created-at'   => 'Создано',
            'updated-at'   => 'Обновлено',
        ],

        'filters' => [
            'trashed' => 'Trashed',
            'status'  => 'Статус',
            'country' => 'Страна',
        ],

        'header-actions' => [
            'create' => [
                'notification' => [
                    'title' => 'Branch создано',
                    'body'  => 'branch есть создано successfully.',
                ],
            ],
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Branch обновлено',
                    'body'  => 'branch есть обновлено successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Branch удалено',
                    'body'  => 'branch есть удалено successfully.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Branch restored',
                    'body'  => 'branch есть restored successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Branches restored',
                    'body'  => 'branches есть restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Branches удалено',
                    'body'  => 'branches есть удалено successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Branches force удалено',
                    'body'  => 'branches есть force удалено successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'tabs' => [
            'general-information' => [
                'title' => 'Общее Сведения',

                'sections' => [
                    'branch-information' => [
                        'title' => 'Branch Сведения',

                        'entries' => [
                            'company-name'                => 'Компания Название',
                            'registration-number'         => 'Registration Номер',
                            'tax-id'                      => 'Налог Ид.',
                            'registration-number-tooltip' => 'Налог Ид. unique identifier для ваш компания.',
                            'color'                       => 'Цвет',
                        ],
                    ],

                    'branding' => [
                        'title'   => 'Branding',
                        'entries' => [
                            'branch-logo' => 'Branch Логотип',
                        ],
                    ],
                ],
            ],

            'address-information' => [
                'title' => 'Адрес Сведения',

                'sections' => [
                    'address-information' => [
                        'title' => 'Адрес Сведения',

                        'entries' => [
                            'street1'                => 'Street 1',
                            'street2'                => 'Street 2',
                            'city'                   => 'Город',
                            'zip'                    => 'Zip Код',
                            'country'                => 'Страна',
                            'country-currency-name'  => 'Валюта Название',
                            'country-phone-code'     => 'Телефон Код',
                            'country-code'           => 'Код',
                            'country-name'           => 'Страна Название',
                            'country-state-required' => 'Состояние Обязательно',
                            'country-zip-required'   => 'Zip Обязательно',
                            'country-create'         => 'Создать Страна',
                            'state'                  => 'Состояние',
                            'state-name'             => 'Состояние Название',
                            'state-code'             => 'Состояние Код',
                            'zip-code'               => 'Zip Код',
                            'state-create'           => 'Создать Состояние',
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
                            'currency-create'         => 'Создать Валюта',
                            'company-foundation-date' => 'Компания Foundation Дата',
                            'status'                  => 'Статус',
                        ],
                    ],
                ],
            ],

            'contact-information' => [
                'title' => 'Контакт Сведения',

                'sections' => [
                    'contact-information' => [
                        'title' => 'Контакт Сведения',

                        'entries' => [
                            'email-address' => 'Эл. почта Адрес',
                            'phone-number'  => 'Телефон Номер',
                            'mobile-number' => 'Телефон Номер',
                        ],
                    ],
                ],
            ],
        ],
    ],
];
