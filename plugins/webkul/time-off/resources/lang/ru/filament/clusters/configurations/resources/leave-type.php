<?php

return [
    'title'      => 'Тип отпуска',
    'navigation' => [
        'title' => 'Тип отпуска',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Общая информация',
                'fields' => [
                    'name'                => 'Название',
                    'approval'            => 'Утверждение',
                    'requires-allocation' => 'Требуется распределение',
                    'employee-requests'   => 'Запросы сотрудников',
                    'display-option'      => 'Отображение',
                ],
            ],
            'display-option' => [
                'title'  => 'Отображение',
                'fields' => [
                    'color' => 'Цвет',
                ],
            ],
            'configuration' => [
                'title' => 'Конфигурация',

                'fields' => [
                    'notified-time-off-officers'          => 'Уведомляемые ответственные за отпуска',
                    'take-time-off-in'                    => 'Единица отпуска',
                    'public-holiday-included'             => 'С учётом государственных праздников',
                    'allow-to-attach-supporting-document' => 'Разрешить прикреплять подтверждающий документ',
                    'show-on-dashboard'                   => 'Показывать на панели',
                    'allow-negative-cap'                  => 'Разрешить отрицательный остаток',
                    'kind-off-time'                       => 'Вид времени',
                    'max-negative-cap'                    => 'Макс. отрицательный остаток',
                    'kind-of-time'                        => 'Вид отпуска',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'                      => 'Название',
            'company-name'              => 'Компания',
            'color'                     => 'Цвет',
            'notified-time-officers'    => 'Уведомляемые ответственные',
            'time-off-approval'         => 'Утверждение отпуска',
            'requires-allocation'       => 'Требуется распределение',
            'allocation-approval'       => 'Утверждение распределения',
            'employee-request'          => 'Запрос сотрудника',
        ],

        'filters' => [
            'name'                => 'Название',
            'company-name'        => 'Компания',
            'time-off-approval'   => 'Утверждение отпуска',
            'requires-allocation' => 'Требуется распределение',
            'time-type'           => 'Тип времени',
            'request-unit'        => 'Единица запроса',
            'created-by'          => 'Создал',
            'employee-request'    => 'Запрос сотрудника',
            'created-at'          => 'Создано',
            'updated-at'          => 'Обновлено',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Тип отпуска удалён',
                    'body'  => 'Тип отпуска успешно удалён.',
                ],
            ],
            'restore' => [
                'notification' => [
                    'title' => 'Тип отпуска восстановлен',
                    'body'  => 'Тип отпуска успешно восстановлен.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Тип отпуска восстановлен',
                    'body'  => 'Тип отпуска успешно восстановлен.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Тип отпуска удалён',
                    'body'  => 'Тип отпуска успешно удалён.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Тип отпуска удалён безвозвратно',
                        'body'  => 'Тип отпуска успешно удалён безвозвратно.',
                    ],
                    'error' => [
                        'title' => 'Не удалось удалить тип отпуска',
                        'body'  => 'Тип отпуска нельзя удалить, так как он сейчас используется.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'   => 'Общая информация',
                'entries' => [
                    'name'                => 'Название',
                    'approval'            => 'Утверждение',
                    'requires-allocation' => 'Требуется распределение',
                    'employee-requests'   => 'Запросы сотрудников',
                    'display-option'      => 'Отображение',
                ],
            ],
            'display-option' => [
                'title'   => 'Отображение',
                'entries' => [
                    'color' => 'Цвет',
                ],
            ],
            'configuration' => [
                'title' => 'Конфигурация',

                'entries' => [
                    'notified-time-off-officers'          => 'Уведомляемые ответственные за отпуска',
                    'take-time-off-in'                    => 'Единица отпуска',
                    'public-holiday-included'             => 'С учётом государственных праздников',
                    'allow-to-attach-supporting-document' => 'Разрешить прикреплять подтверждающий документ',
                    'show-on-dashboard'                   => 'Показывать на панели',
                    'kind-off-time'                       => 'Вид времени',
                    'max-negative-cap'                    => 'Макс. отрицательный остаток',
                    'kind-of-time'                        => 'Вид отпуска',
                ],
            ],
        ],
    ],
];
