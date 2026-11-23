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
                'account-name'           => 'Счёт Название',
                'accounting'             => 'Учёт',
                'account-type'           => 'Счёт Тип',
                'parent-account'         => 'Parent Счёт',
                'parent-account-helper'  => 'Выбрать existing счёт до сделать этот sub-счёт.',
                'default-taxes'          => 'По умолчанию Налоги',
                'tags'                   => 'Метки',
                'journals'               => 'Журналы',
                'journals-helper'        => 'Suggested автоматически based вкл. выбрано Счёт Тип. Вы можно override selection.',
                'currency'               => 'Валюта',
                'deprecated'             => 'Deprecated',
                'reconcile'              => 'Разрешить Сверить',
                'non-trade'              => 'Non Trade',
                'companies'              => 'Компании',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'code'           => 'Код',
            'account-name'   => 'Счёт Название',
            'account-type'   => 'Счёт',
            'parent-account' => 'Parent Счёт',
            'currency'       => 'Валюта',
            'journals'       => 'Журналы',
            'reconcile'      => 'Разрешить Сверить',
        ],

        'grouping' => [
            'account-type' => 'Счёт Тип',
        ],

        'filters' => [
            'account-type'     => 'Счёт Тип',
            'parent-account'   => 'Parent Счёт',
            'allow-reconcile'  => 'Разрешить Сверить',
            'currency'         => 'Валюта',
            'account-journals' => 'Журналы',
            'non-trade'        => 'Non Trade',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Счёт обновлено',
                    'body'  => 'счёт есть обновлено successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Счёт удалено',
                        'body'  => 'счёт есть удалено successfully.',
                    ],

                    'error' => [
                        'title' => 'Счёт deletion не удалось',
                        'body'  => 'счёт could не быть удалено потому что это есть associated журнал позиции.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Счета удалено',
                        'body'  => 'счета есть удалено successfully.',
                    ],

                    'error' => [
                        'title' => 'Счета deletion не удалось',
                        'body'  => 'счета could не быть удалено потому что они есть associated журнал позиции.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'entries' => [
                'code'           => 'Код',
                'account-name'   => 'Счёт Название',
                'accounting'     => 'Учёт',
                'account-type'   => 'Счёт Тип',
                'parent-account' => 'Parent Счёт',
                'sub-accounts'   => 'Sub Счета',
                'default-taxes'  => 'По умолчанию Налоги',
                'tags'           => 'Метки',
                'journals'       => 'Журналы',
                'currency'       => 'Валюта',
                'deprecated'     => 'Deprecated',
                'reconcile'      => 'Сверить',
                'non-trade'      => 'Non Trade',
            ],
        ],
    ],
];
