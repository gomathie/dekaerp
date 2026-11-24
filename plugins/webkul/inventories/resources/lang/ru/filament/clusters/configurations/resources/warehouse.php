<?php

return [
    'navigation' => [
        'title' => 'Склады',
        'group' => 'Управление складом',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Общее',
                'fields' => [
                    'name'                    => 'Название',
                    'name-placeholder'        => 'напр. Центральный склад',
                    'code'                    => 'Краткое название',
                    'code-placeholder'        => 'напр. ЦС',
                    'code-hint-tooltip'       => 'Краткое название служит идентификатором склада.',
                    'company'                 => 'Компания',
                    'multi-warehouse-warning' => 'При создании нового склада автоматически включается настройка «Места хранения».',
                    'address'                 => 'Адрес',
                ],
            ],

            'settings' => [
                'title'  => 'Настройки',

                'fields' => [
                    'shipment-management'              => 'Управление отправками',
                    'incoming-shipments'               => 'Входящие отправки',
                    'incoming-shipments-hint-tooltip'  => 'Входящий маршрут по умолчанию',
                    'outgoing-shipments'               => 'Исходящие отправки',
                    'outgoing-shipments-hint-tooltip'  => 'Исходящий маршрут по умолчанию',
                    'manufacture'                      => 'Производство',
                    'manufacture-hint-tooltip'         => 'Маршрут производства по умолчанию',
                    'resupply-management'              => 'Управление пополнением',
                    'resupply-management-hint-tooltip' => 'Маршруты для пополнения этого склада с выбранных складов будут созданы автоматически.',
                    'resupply-from'                    => 'Пополнять с',
                ],
            ],

            'additional' => [
                'title'  => 'Дополнительная информация',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'       => 'Название',
            'code'       => 'Краткое название',
            'company'    => 'Компания',
            'address'    => 'Адрес',
            'deleted-at' => 'Удалено',
            'created-at' => 'Создано',
            'updated-at' => 'Обновлено',
        ],

        'groups' => [
            'address'       => 'Адрес',
            'company'       => 'Компания',
            'created-at'    => 'Создано',
            'updated-at'    => 'Обновлено',
        ],

        'filters' => [
            'company' => 'Компания',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Склад восстановлен',
                    'body'  => 'Склад успешно восстановлен.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Склад удалён',
                    'body'  => 'Склад успешно удалён.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Склад удалён безвозвратно',
                        'body'  => 'Склад успешно удалён безвозвратно.',
                    ],

                    'error' => [
                        'title' => 'Не удалось удалить склад',
                        'body'  => 'Склад нельзя удалить, так как он используется.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Склады восстановлены',
                    'body'  => 'Склады успешно восстановлены.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Склады удалены',
                    'body'  => 'Склады успешно удалены.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Склады удалены безвозвратно',
                        'body'  => 'Склады успешно удалены безвозвратно.',
                    ],

                    'error' => [
                        'title' => 'Не удалось удалить склады',
                        'body'  => 'Склады нельзя удалить, так как они используются.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Общая информация',

                'entries' => [
                    'name'    => 'Название склада',
                    'code'    => 'Код склада',
                    'company' => 'Компания',
                    'address' => 'Адрес',
                ],
            ],

            'settings' => [
                'title' => 'Настройки',

                'entries' => [
                    'shipment-management' => 'Управление отправками',
                    'incoming-shipments'  => 'Входящие отправки',
                    'outgoing-shipments'  => 'Исходящие отправки',
                    'resupply-management' => 'Управление пополнением',
                    'resupply-from'       => 'Пополнять с',
                ],
            ],

            'record-information' => [
                'title' => 'Сведения о записи',

                'entries' => [
                    'created-by'   => 'Создал',
                    'created-at'   => 'Создано',
                    'last-updated' => 'Последнее обновление',
                ],
            ],
        ],
    ],
];
