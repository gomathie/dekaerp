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
            'needs-attention'                 => 'Проверьте отмеченные поля',
            'cannot-remove-last-admin'        => 'Нельзя снять роль администратора с последнего администратора.',
            'company-invalid'                 => 'Одна или несколько выбранных компаний недействительны.',
            'company-not-assigned'            => 'Вы можете назначать пользователей только в компании, назначенные вашей учётной записи.',
            'company-required'                => 'Выберите хотя бы одну компанию.',
            'first-user-must-be-admin'        => 'Первому пользователю системы нужно назначить роль администратора.',
            'role-invalid'                    => 'Выберите хотя бы одну допустимую роль.',
            'role-not-assignable'             => 'Вы можете назначать только незащищённые роли, все права которых есть у вас.',
            'self-administration-forbidden'   => 'Нельзя менять собственные роли или привязку к компаниям.',
            'target-administration-forbidden' => 'Изменять защищённого администратора может только Super Admin.',
        ],

        'sections' => [
            'general-information' => [
                'title'  => 'Общие сведения',
                'fields' => [
                    'name'                         => 'Имя',
                    'email'                        => 'Эл. почта',
                    'username'                     => 'Имя пользователя',
                    'username-helper'              => 'Необязательно. Позволяет входить по короткому имени вместо эл. почты: буквы, цифры, точка, дефис или подчёркивание, от 3 до 50 символов, без @.',
                    'password'                     => 'Пароль',
                    'password-confirmation'        => 'Подтверждение пароля',
                    'must-change-password'         => 'Требовать новый пароль при следующем входе',
                    'must-change-password-helper'  => 'Используйте, если вы сами задали и передали пароль. Пользователь не сможет уйти со страницы профиля, пока не сменит пароль; после этого требование снимается.',
                ],
            ],

            'permissions' => [
                'title'  => 'Права',
                'fields' => [
                    'roles'                                    => 'Роли',
                    'permissions'                              => 'Права',
                    'resource-permission'                      => 'Доступ к записям',
                    'resource-permission-self-change-disabled' => 'Нельзя изменить собственный доступ к записям. Попросите об этом другого администратора.',
                    'can-see-dashboard'                        => 'Видеть панели',
                    'can-see-dashboard-helper'                 => 'Если выключено, обзор и все панели скрыты от пользователя.',
                    'can-see-money-figures'                    => 'Видеть финансовые показатели',
                    'can-see-money-figures-helper'             => 'Если выключено, панели продаж, счетов, закупок и бухгалтерии скрыты.',
                    'teams'                                    => 'Команды',
                ],
            ],

            'avatar' => [
                'title' => 'Аватар',
            ],

            'lang-and-status' => [
                'title'  => 'Язык и статус',
                'fields' => [
                    'language' => 'Предпочитаемый язык',
                    'status'   => 'Статус',
                ],
            ],

            'multi-company' => [
                'title'                       => 'Несколько компаний',
                'allowed-companies'           => 'Разрешённые компании',
                'default-company'             => 'Компания по умолчанию',
                'default-company-not-allowed' => 'Компания по умолчанию должна быть одной из разрешённых.',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'avatar'              => 'Аватар',
            'name'                => 'Имя',
            'email'               => 'Эл. почта',
            'teams'               => 'Команды',
            'role'                => 'Роль',
            'resource-permission' => 'Доступ к записям',
            'default-company'     => 'Компания по умолчанию',
            'allowed-company'     => 'Разрешённая компания',
            'created-by'          => 'Создал',
            'created-at'          => 'Создан',
            'updated-at'          => 'Обновлён',
        ],

        'filters' => [
            'resource-permission' => 'Доступ к записям',
            'teams'               => 'Команды',
            'roles'               => 'Роли',
            'default-company'     => 'Компания по умолчанию',
            'allowed-companies'   => 'Разрешённые компании',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Пользователь изменён',
                    'body'  => 'Пользователь успешно изменён.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Пользователь удалён',
                    'body'  => 'Пользователь успешно удалён.',
                    'error' => [
                        'title' => 'Пользователя нельзя удалить',
                        'body'  => 'Это пользователь по умолчанию, либо вы пытаетесь удалить себя.',
                    ],
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Пользователь восстановлен',
                    'body'  => 'Пользователь успешно восстановлен.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Пользователи восстановлены',
                    'body'  => 'Пользователи успешно восстановлены.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Пользователи удалены',
                    'body'  => 'Пользователи успешно удалены.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Пользователи удалены безвозвратно',
                    'body'  => 'Пользователи успешно удалены безвозвратно.',
                    'error' => [
                        'title' => 'Не удалось удалить пользователя',
                        'body'  => 'Пользователя нельзя удалить, так как он используется.',
                    ],
                ],
            ],
        ],

        'empty-state-actions' => [
            'create' => [
                'notification' => [
                    'title' => 'Пользователи созданы',
                    'body'  => 'Пользователи успешно созданы.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general-information' => [
                'title'   => 'Общие сведения',
                'entries' => [
                    'name'                  => 'Имя',
                    'email'                 => 'Эл. почта',
                    'password'              => 'Пароль',
                    'password-confirmation' => 'Подтверждение пароля',
                ],
            ],

            'permissions' => [
                'title'   => 'Права',
                'entries' => [
                    'roles'               => 'Роли',
                    'permissions'         => 'Права',
                    'resource-permission' => 'Доступ к записям',
                    'teams'               => 'Команды',
                ],
            ],

            'avatar' => [
                'title' => 'Аватар',
            ],

            'lang-and-status' => [
                'title'   => 'Язык и статус',
                'entries' => [
                    'language' => 'Предпочитаемый язык',
                    'status'   => 'Статус',
                ],
            ],

            'multi-company' => [
                'title'                       => 'Несколько компаний',
                'allowed-companies'           => 'Разрешённые компании',
                'default-company'             => 'Компания по умолчанию',
                'default-company-not-allowed' => 'Компания по умолчанию должна быть одной из разрешённых.',
            ],
        ],
    ],
];
