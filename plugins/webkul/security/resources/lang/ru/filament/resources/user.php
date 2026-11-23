<?php

return [
    'title' => 'Пользователи',

    'navigation' => [
        'title' => 'Пользователи',
    ],

    'global-search' => [
        'email' => 'Эл. почта',
    ],

    'form' => [
        'validation' => [
            'needs-attention'                 => 'Некоторые поля need attention',            'cannot-remove-last-admin'        => 'Cannot remove the admin role from the last admin user.',
            'company-invalid'                 => 'One или больше выбрано компании недействительно.',
            'company-not-assigned'            => 'Вы май назначить пользователи только до компании назначен до ваш счёт.',
            'company-required'                => 'Выбрать в наименее one компания.',
            'first-user-must-be-admin'        => 'первый пользователь в система нужно быть назначен администратор роль.',
            'role-invalid'                    => 'Выбрать в наименее one действительно роль.',
            'role-not-assignable'             => 'Вы май назначить только non-protected роли whose права вы possess.',
            'self-administration-forbidden'   => 'Вы нельзя изменить ваш собственную роли или компания assignments.',
            'target-administration-forbidden' => 'Только Super Администратор май modify protected администратор.',
        ],

        'sections' => [
            'general-information' => [
                'title'  => 'Общее Сведения',
                'fields' => [
                    'name'                         => 'Название',
                    'email'                        => 'Эл. почта',
                    'username'                     => 'Username',
                    'username-helper'              => 'Необязательно. Lets этот person войти с short название instead эл. почта: letters, numbers, dot, dash или underscore, 3 до 50 characters, нет @.',
                    'password'                     => 'Пароль',
                    'password-confirmation'        => 'Пароль Confirmation',
                    'must-change-password'         => 'Require новое пароль в далее sign-в',
                    'must-change-password-helper'  => 'Использовать этот когда вы задать пароль yourself и passed это вкл.. Они быть held вкл. их профиль страница до они replace это, и requirement clears как скоро как они.',
                ],
            ],

            'permissions' => [
                'title'  => 'Права',
                'fields' => [
                    'roles'                                    => 'Роли',
                    'permissions'                              => 'Права',
                    'resource-permission'                      => 'Resource Право',
                    'resource-permission-self-change-disabled' => 'Вы нельзя изменить ваш собственную resource право. Ask another администратор до обновить это.',
                    'can-see-dashboard'                        => 'See панель',
                    'can-see-dashboard-helper'                 => 'Выкл. hides Обзор и каждый панель для этот пользователь.',
                    'can-see-money-figures'                    => 'See money figures',
                    'can-see-money-figures-helper'             => 'Выкл. hides продажи, счёт, закупка и учёт dashboards.',
                    'teams'                                    => 'Teams',
                ],
            ],

            'avatar' => [
                'title' => 'Avatar',
            ],

            'lang-and-status' => [
                'title'  => 'Язык & Статус',
                'fields' => [
                    'language' => 'Preferred Язык',
                    'status'   => 'Статус',
                ],
            ],

            'multi-company' => [
                'title'                       => 'Multi Компания',
                'allowed-companies'           => 'Разрешено Компании',
                'default-company'             => 'По умолчанию Компания',
                'default-company-not-allowed' => 'по умолчанию компания нужно быть one разрешено компании.',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'avatar'              => 'Avatar',
            'name'                => 'Название',
            'email'               => 'Эл. почта',
            'teams'               => 'Teams',
            'role'                => 'Роль',
            'resource-permission' => 'Resource Право',
            'default-company'     => 'По умолчанию Компания',
            'allowed-company'     => 'Разрешено Компания',
            'created-by'          => 'Создано От',
            'created-at'          => 'Создано',
            'updated-at'          => 'Обновлено',
        ],

        'filters' => [
            'resource-permission' => 'Resource Право',
            'teams'               => 'Teams',
            'roles'               => 'Роли',
            'default-company'     => 'По умолчанию Компания',
            'allowed-companies'   => 'Разрешено Компании',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Пользователь edited',
                    'body'  => 'пользователь есть edited successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Пользователь удалено',
                    'body'  => 'пользователь есть удалено successfully.',
                    'error' => [
                        'title' => 'Пользователь Нельзя Быть Удалено',
                        'body'  => 'Этот по умолчанию пользователь или вы нельзя удалить yourself.',
                    ],
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Пользователь restored',
                    'body'  => 'пользователь есть restored successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Пользователи restored',
                    'body'  => 'пользователи есть restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Пользователи удалено',
                    'body'  => 'пользователи есть удалено successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Пользователи force удалено',
                    'body'  => 'пользователи есть force удалено successfully.',
                    'error' => [
                        'title' => 'Пользователь could не быть удалено',
                        'body'  => 'пользователь нельзя быть удалено потому что это currently в использовать.',
                    ],
                ],
            ],
        ],

        'empty-state-actions' => [
            'create' => [
                'notification' => [
                    'title' => 'Пользователи создано',
                    'body'  => 'пользователи есть создано successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general-information' => [
                'title'   => 'Общее Сведения',
                'entries' => [
                    'name'                  => 'Название',
                    'email'                 => 'Эл. почта',
                    'password'              => 'Пароль',
                    'password-confirmation' => 'Пароль Confirmation',
                ],
            ],

            'permissions' => [
                'title'   => 'Права',
                'entries' => [
                    'roles'               => 'Роли',
                    'permissions'         => 'Права',
                    'resource-permission' => 'Resource Право',
                    'teams'               => 'Teams',
                ],
            ],

            'avatar' => [
                'title' => 'Avatar',
            ],

            'lang-and-status' => [
                'title'   => 'Язык & Статус',
                'entries' => [
                    'language' => 'Preferred Язык',
                    'status'   => 'Статус',
                ],
            ],

            'multi-company' => [
                'title'                       => 'Multi Компания',
                'allowed-companies'           => 'Разрешено Компании',
                'default-company'             => 'По умолчанию Компания',
                'default-company-not-allowed' => 'по умолчанию компания нужно быть one разрешено компании.',
            ],
        ],
    ],
];
