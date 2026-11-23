<?php

return [
    'title'      => 'Отпуск Тип',
    'navigation' => [
        'title' => 'Отпуск Тип',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Общее Сведения',
                'fields' => [
                    'name'                => 'Заголовок',
                    'approval'            => 'Approval',
                    'requires-allocation' => 'Requires Allocation',
                    'employee-requests'   => 'Сотрудник Requests',
                    'display-option'      => 'Display Option',
                ],
            ],
            'display-option' => [
                'title'  => 'Display Option',
                'fields' => [
                    'color' => 'Цвет',
                ],
            ],
            'configuration' => [
                'title' => 'Конфигурация',

                'fields' => [
                    'notified-time-off-officers'          => 'Notified Отгул и отпуск Officers',
                    'take-time-off-in'                    => 'Take Отгул и отпуск В',
                    'public-holiday-included'             => 'Публичный Holiday Включено',
                    'allow-to-attach-supporting-document' => 'Разрешить до Attach Supporting Документ',
                    'show-on-dashboard'                   => 'Показать вкл. Панель',
                    'allow-negative-cap'                  => 'Разрешить Negative Cap',
                    'kind-off-time'                       => 'Kind Time',
                    'max-negative-cap'                    => 'Max Negative Cap',
                    'kind-of-time'                        => 'Kind Отгул и отпуск',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'                      => 'Название',
            'company-name'              => 'Компания',
            'color'                     => 'Цвет',
            'notified-time-officers'    => 'Notified Отгул и отпускicers',
            'time-off-approval'         => 'Отгул и отпуск Approval',
            'requires-allocation'       => 'Requires Allocation',
            'allocation-approval'       => 'Allocation Approval',
            'employee-request'          => 'Сотрудник Request',
        ],

        'filters' => [
            'name'                => 'Название',
            'company-name'        => 'Компания',
            'time-off-approval'   => 'Отгул и отпуск Approval',
            'requires-allocation' => 'Requires Allocation',
            'time-type'           => 'Time Тип',
            'request-unit'        => 'Request Единица',
            'created-by'          => 'Создано От',
            'employee-request'    => 'Сотрудник Request',
            'created-at'          => 'Создано',
            'updated-at'          => 'Обновлено',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Отпуск Тип удалено',
                    'body'  => 'Отпуск тип есть удалено successfully.',
                ],
            ],
            'restore' => [
                'notification' => [
                    'title' => 'Отпуск Тип restored',
                    'body'  => 'Отпуск тип есть restored successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Отпуск Тип restored',
                    'body'  => 'Отпуск Тип есть restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Отпуск Тип удалено',
                    'body'  => 'Отпуск Тип есть удалено successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Отпуск Тип force удалено',
                        'body'  => 'Отпуск Тип есть force удалено successfully.',
                    ],
                    'error' => [
                        'title' => 'Отпуск Тип could не быть удалено',
                        'body'  => 'Отпуск Тип нельзя быть удалено потому что это currently в использовать.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'   => 'Общее Сведения',
                'entries' => [
                    'name'                => 'Заголовок',
                    'approval'            => 'Approval',
                    'requires-allocation' => 'Requires Allocation',
                    'employee-requests'   => 'Сотрудник Requests',
                    'display-option'      => 'Display Option',
                ],
            ],
            'display-option' => [
                'title'   => 'Display Option',
                'entries' => [
                    'color' => 'Цвет',
                ],
            ],
            'configuration' => [
                'title' => 'Конфигурация',

                'entries' => [
                    'notified-time-off-officers'          => 'Notified Отгул и отпуск Officers',
                    'take-time-off-in'                    => 'Take Отгул и отпуск В',
                    'public-holiday-included'             => 'Публичный Holiday Включено',
                    'allow-to-attach-supporting-document' => 'Разрешить до Attach Supporting Документ',
                    'show-on-dashboard'                   => 'Показать вкл. Панель',
                    'kind-off-time'                       => 'Kind Time',
                    'max-negative-cap'                    => 'Max Negative Cap',
                    'kind-of-time'                        => 'Kind Отгул и отпуск',
                ],
            ],
        ],
    ],
];
