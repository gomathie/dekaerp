<?php

return [
    'navigation' => [
        'title' => 'Операции',
        'group' => 'Конфигурация',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Общее',
                'fields' => [
                    'name'              => 'Операция',
                    'name-placeholder'  => 'напр. Резка',
                    'bill-of-material'  => 'Спецификация',
                    'work-center'       => 'Рабочий центр',
                    'apply-on-variants' => 'Применять к вариантам',
                    'company'           => 'Компания',
                    'blocked-by'        => 'Заблокирована операциями',
                ],
            ],
            'settings' => [
                'title'  => 'Настройки',
                'fields' => [
                    'time-mode'                  => 'Расчёт длительности',
                    'time-mode-batch'            => 'На основе',
                    'time-mode-batch-prefix'     => 'последних',
                    'time-mode-batch-suffix'     => 'рабочих нарядов',
                    'manual-cycle-time'          => 'Длительность по умолчанию',
                    'manual-cycle-time-suffix'   => 'минут',
                ],
            ],
            'worksheet' => [
                'title'  => 'Рабочая инструкция',
                'fields' => [
                    'worksheet'                => 'Инструкция',
                    'pdf'                      => 'PDF',
                    'google-slide'             => 'Google Slides',
                    'google-slide-placeholder' => 'Ссылка на Google Slides',
                    'description'              => 'Описание',
                    'description-placeholder'  => 'Описание операции…',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'              => 'Операция',
            'bill-of-material'  => 'Спецификация',
            'work-center'       => 'Рабочий центр',
            'time-mode'         => 'Расчёт длительности',
            'manual-cycle-time' => 'Длительность по умолчанию',
            'worksheet-type'    => 'Инструкция',
            'deleted-at'        => 'Удалена',
            'created-at'        => 'Создана',
            'updated-at'        => 'Обновлена',
        ],
        'filters' => [
            'work-center'    => 'Рабочий центр',
            'time-mode'      => 'Расчёт длительности',
            'worksheet-type' => 'Инструкция',
        ],
        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Операция восстановлена',
                    'body'  => 'Операция успешно восстановлена.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Операция архивирована',
                    'body'  => 'Операция успешно архивирована.',
                ],
            ],
            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Операция удалена',
                        'body'  => 'Операция удалена безвозвратно.',
                    ],
                    'error' => [
                        'title' => 'Не удалось удалить операцию',
                        'body'  => 'Операцию нельзя удалить, так как она используется.',
                    ],
                ],
            ],
        ],
        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Операции восстановлены',
                    'body'  => 'Выбранные операции успешно восстановлены.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Операции архивированы',
                    'body'  => 'Выбранные операции успешно архивированы.',
                ],
            ],
            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Операции удалены',
                        'body'  => 'Выбранные операции удалены безвозвратно.',
                    ],
                    'error' => [
                        'title' => 'Не удалось удалить операции',
                        'body'  => 'Одна или несколько выбранных операций используются.',
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
                    'name'              => 'Операция',
                    'bill-of-material'  => 'Спецификация',
                    'work-center'       => 'Рабочий центр',
                    'apply-on-variants' => 'Применять к вариантам',
                    'company'           => 'Компания',
                ],
            ],
            'settings' => [
                'title'   => 'Настройки',
                'entries' => [
                    'time-mode'                => 'Расчёт длительности',
                    'time-mode-batch'          => 'На основе',
                    'manual-cycle-time'        => 'Длительность по умолчанию',
                    'manual-cycle-time-suffix' => 'минут',
                ],
            ],
            'worksheet' => [
                'title'   => 'Рабочая инструкция',
                'entries' => [
                    'worksheet'    => 'Инструкция',
                    'pdf'          => 'PDF',
                    'google-slide' => 'Google Slides',
                    'description'  => 'Описание',
                ],
            ],
            'record-information' => [
                'title'   => 'Сведения о записи',
                'entries' => [
                    'created-by'   => 'Автор',
                    'created-at'   => 'Создана',
                    'last-updated' => 'Последнее обновление',
                ],
            ],
        ],
    ],
];
