<?php

return [
    'navigation' => [
        'title' => 'Склады',
        'group' => 'Склад Management',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Общее',
                'fields' => [
                    'name'                    => 'Название',
                    'name-placeholder'        => 'eg. Central Склад',
                    'code'                    => 'Short Название',
                    'code-placeholder'        => 'eg. CW',
                    'code-hint-tooltip'       => 'short название serves как identifier для склад.',
                    'company'                 => 'Компания',
                    'multi-warehouse-warning' => 'Creating новое склад автоматически activate Storage Места настройка.',
                    'address'                 => 'Адрес',
                ],
            ],

            'settings' => [
                'title'  => 'Настройки',

                'fields' => [
                    'shipment-management'              => 'Отправка Management',
                    'incoming-shipments'               => 'Incoming Отправки',
                    'incoming-shipments-hint-tooltip'  => 'По умолчанию incoming маршрут до follow',
                    'outgoing-shipments'               => 'Outgoing Отправки',
                    'outgoing-shipments-hint-tooltip'  => 'По умолчанию outgoing маршрут до follow',
                    'manufacture'                      => 'Производство',
                    'manufacture-hint-tooltip'         => 'По умолчанию производство маршрут до follow',
                    'resupply-management'              => 'Resupply Management',
                    'resupply-management-hint-tooltip' => 'Маршруты быть автоматически generated до resupply этот склад от выбрано склады.',
                    'resupply-from'                    => 'Resupply От',
                ],
            ],

            'additional' => [
                'title'  => 'Additional Сведения',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'       => 'Название',
            'code'       => 'Short Название',
            'company'    => 'Компания',
            'address'    => 'Адрес',
            'deleted-at' => 'удалено',
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
                    'title' => 'Склад restored',
                    'body'  => 'склад есть restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Склад удалено',
                    'body'  => 'склад есть удалено successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Склад force удалено',
                        'body'  => 'склад есть force удалено successfully.',
                    ],

                    'error' => [
                        'title' => 'Склад could не быть удалено',
                        'body'  => 'склад нельзя быть удалено потому что это currently в использовать.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Склады restored',
                    'body'  => 'склады есть restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Склады удалено',
                    'body'  => 'склады есть удалено successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Склады force удалено',
                        'body'  => 'склады есть force удалено successfully.',
                    ],

                    'error' => [
                        'title' => 'Склады could не быть удалено',
                        'body'  => 'склады нельзя быть удалено потому что они currently в использовать.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Общее Сведения',

                'entries' => [
                    'name'    => 'Склад Название',
                    'code'    => 'Склад Код',
                    'company' => 'Компания',
                    'address' => 'Адрес',
                ],
            ],

            'settings' => [
                'title' => 'Настройки',

                'entries' => [
                    'shipment-management' => 'Отправка Management',
                    'incoming-shipments'  => 'Incoming Отправки',
                    'outgoing-shipments'  => 'Outgoing Отправки',
                    'resupply-management' => 'Resupply Management',
                    'resupply-from'       => 'Resupply От',
                ],
            ],

            'record-information' => [
                'title' => 'Запись Сведения',

                'entries' => [
                    'created-by'   => 'Создано От',
                    'created-at'   => 'Создано',
                    'last-updated' => 'Последний Обновлено',
                ],
            ],
        ],
    ],
];
