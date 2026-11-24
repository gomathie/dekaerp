<?php

return [
    'global-search' => [
        'code' => 'Код',
        'type' => 'Тип',
    ],

    'form' => [
        'sections' => [
            'fields' => [
                'code'                   => 'Код',
                'account-name'           => 'Название счёта',
                'accounting'             => 'Учёт',
                'account-type'           => 'Тип счёта',
                'parent-account'         => 'Родительский счёт',
                'parent-account-helper'  => 'Выберите существующий счёт, чтобы сделать этот счёт его субсчётом.',
                'default-taxes'          => 'Налоги по умолчанию',
                'tags'                   => 'Теги',
                'journals'               => 'Журналы',
                'journals-helper'        => 'Подбирается автоматически по выбранному типу счёта. Выбор можно изменить.',
                'currency'               => 'Валюта',
                'deprecated'             => 'Устаревший',
                'reconcile'              => 'Разрешить сверку',
                'non-trade'              => 'Неторговый',
                'companies'              => 'Компании',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'code'           => 'Код',
            'account-name'   => 'Название счёта',
            'account-type'   => 'Счёт',
            'parent-account' => 'Родительский счёт',
            'currency'       => 'Валюта',
            'journals'       => 'Журналы',
            'reconcile'      => 'Разрешить сверку',
        ],

        'grouping' => [
            'account-type' => 'Тип счёта',
        ],

        'filters' => [
            'account-type'     => 'Тип счёта',
            'parent-account'   => 'Родительский счёт',
            'allow-reconcile'  => 'Разрешить сверку',
            'currency'         => 'Валюта',
            'account-journals' => 'Журналы',
            'non-trade'        => 'Неторговый',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Счёт обновлён',
                    'body'  => 'Счёт успешно обновлён.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Счёт удалён',
                        'body'  => 'Счёт успешно удалён.',
                    ],

                    'error' => [
                        'title' => 'Не удалось удалить счёт',
                        'body'  => 'Счёт нельзя удалить: по нему есть записи в журнале.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Счета удалены',
                        'body'  => 'Счета успешно удалены.',
                    ],

                    'error' => [
                        'title' => 'Не удалось удалить счета',
                        'body'  => 'Счета нельзя удалить: по ним есть записи в журнале.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'entries' => [
                'code'           => 'Код',
                'account-name'   => 'Название счёта',
                'accounting'     => 'Учёт',
                'account-type'   => 'Тип счёта',
                'parent-account' => 'Родительский счёт',
                'sub-accounts'   => 'Субсчета',
                'default-taxes'  => 'Налоги по умолчанию',
                'tags'           => 'Теги',
                'journals'       => 'Журналы',
                'currency'       => 'Валюта',
                'deprecated'     => 'Устаревший',
                'reconcile'      => 'Сверка',
                'non-trade'      => 'Неторговый',
            ],
        ],
    ],
];
