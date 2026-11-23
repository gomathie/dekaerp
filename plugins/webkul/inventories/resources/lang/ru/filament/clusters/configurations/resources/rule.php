<?php

return [
    'navigation' => [
        'title' => 'Rules',
        'group' => 'Склад Management',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Общее',

                'fields' => [
                    'name'                        => 'Название',
                    'action'                      => 'Действие',
                    'operation-type'              => 'Operation Тип',
                    'source-location'             => 'Источник Место',
                    'destination-location'        => 'Назначение Место',
                    'supply-method'               => 'Supply Method',
                    'supply-method-hint-tooltip'  => 'Take От Запас: Товары sourced directly от доступно запас в источник место.<br/>Trigger Another Rule: система ignores доступно запас и searches для запас rule до replenish источник место.<br/>Take От Запас, если Недоступно, Trigger Another Rule: Товары первый taken от доступно запас. Если нет доступно, система действует запас rule до bring товары в источник место.',
                    'automatic-move'              => 'Автоматически Переместить',
                    'automatic-move-hint-tooltip' => 'Вручную Operation: Creates separate запас переместить после текущий one.<br/>Автоматически Нет Step Added: Directly replaces место в original переместить без adding extra step.',

                    'action-information' => [
                        'pull'        => 'Когда товары обязательно в <b>:sourceLocation</b>, :operation generated от <b>:destinationLocation</b> до meet потребность.',
                        'push'        => 'Когда товары reach <b>:sourceLocation</b>,</br><b>:operation</b> generated до перевод их до <b>:destinationLocation</b>.',
                        'buy'         => 'Когда товары needed в <b>:destinationLocation</b>, request для коммерческое предложение создано до fulfill need.',
                        'manufacture' => 'Когда товары needed в <b>:destinationLocation</b>, производство заказ создано до fulfill need.',
                    ],
                ],
            ],

            'settings' => [
                'title'  => 'Настройки',

                'fields' => [
                    'partner-address'              => 'Контрагент Адрес',
                    'partner-address-hint-tooltip' => 'Адрес где goods следует быть delivered. Необязательно.',
                    'lead-time'                    => 'Lead Time (Дни)',
                    'lead-time-hint-tooltip'       => 'ожидается перевод дата быть calculated с помощью этот lead time.',
                ],

                'fieldsets' => [
                    'applicability' => [
                        'title'  => 'Applicability',

                        'fields' => [
                            'route'   => 'Маршрут',
                            'company' => 'Компания',
                        ],
                    ],

                    'propagation' => [
                        'title'  => 'Propagation',

                        'fields' => [
                            'propagation-procurement-group'              => 'Propagation Procurement Группа',
                            'propagation-procurement-group-hint-tooltip' => 'Если выбрано, canceling переместить создано от этот rule также отмена subsequent переместить.',
                            'cancel-next-move'                           => 'Отмена Далее Переместить',
                            'warehouse-to-propagate'                     => 'Склад до Propagate',
                            'warehouse-to-propagate-hint-tooltip'        => 'склад назначен до создано переместить или procurement, который май differ от склад этот rule действует до (e.g., для resupply rules от another склад).',
                        ],
                    ],
                ],

            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'                 => 'Название',
            'action'               => 'Действие',
            'source-location'      => 'Источник Место',
            'destination-location' => 'Назначение Место',
            'route'                => 'Маршрут',
            'deleted-at'           => 'Удалено',
            'created-at'           => 'Создано',
            'updated-at'           => 'Обновлено',
        ],

        'groups' => [
            'action'               => 'Действие',
            'source-location'      => 'Источник Место',
            'destination-location' => 'Назначение Место',
            'route'                => 'Маршрут',
            'created-at'           => 'Создано',
            'updated-at'           => 'Обновлено',
        ],

        'filters' => [
            'action'               => 'Действие',
            'source-location'      => 'Источник Место',
            'destination-location' => 'Назначение Место',
            'route'                => 'Маршрут',
            'company'              => 'Компания',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Rule обновлено',
                    'body'  => 'rule есть обновлено successfully.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Rule restored',
                    'body'  => 'rule есть restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Rule удалено',
                    'body'  => 'rule есть удалено successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Rule force удалено',
                        'body'  => 'rule есть force удалено successfully.',
                    ],

                    'error' => [
                        'title' => 'Rule could не быть удалено',
                        'body'  => 'rule нельзя быть удалено потому что это currently в использовать.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Rules restored',
                    'body'  => 'rules есть restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Rules удалено',
                    'body'  => 'rules есть удалено successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Rules force удалено',
                        'body'  => 'rules есть force удалено successfully.',
                    ],

                    'error' => [
                        'title' => 'Rules could не быть удалено',
                        'body'  => 'rules нельзя быть удалено потому что они currently в использовать.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Rule Подробности',

                'description' => [
                    'pull' => 'Когда товары обязательно в <b>:sourceLocation</b>, <b>:operation</b> generated от <b>:destinationLocation</b> до meet потребность.',
                    'push' => 'Когда товары reach в <b>:sourceLocation</b>, <b>:operation</b> generated до перевод их до <b>:destinationLocation</b>.',
                ],

                'entries' => [
                    'name'                 => 'Rule Название',
                    'action'               => 'Действие',
                    'operation-type'       => 'Operation Тип',
                    'source-location'      => 'Источник Место',
                    'destination-location' => 'Назначение Место',
                    'route'                => 'Маршрут',
                    'company'              => 'Компания',
                    'partner-address'      => 'Контрагент Адрес',
                    'lead-time'            => 'Lead Time',
                    'action-information'   => 'Действие Сведения',
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
