<?php

return [
    'title' => 'Типы действий',

    'navigation' => [
        'title' => 'Типы действий',
        'group' => 'Сотрудники',
    ],

    'form' => [
        'sections' => [
            'activity-type-details' => [
                'title' => 'Общие сведения',

                'fields' => [
                    'name'                => 'Тип действия',
                    'name-tooltip'        => 'Введите официальное название типа действия',
                    'action'              => 'Действие',
                    'default-user'        => 'Пользователь по умолчанию',
                    'summary'             => 'Краткое описание',
                    'note'                => 'Примечание',
                ],
            ],

            'delay-information' => [
                'title' => 'Задержка',

                'fields' => [
                    'delay-count'            => 'Величина задержки',
                    'delay-unit'             => 'Единица задержки',
                    'delay-form'             => 'Отсчёт задержки от',
                    'delay-form-helper-text' => 'От чего отсчитывается задержка',
                ],
            ],

            'advanced-information' => [
                'title' => 'Дополнительно',

                'fields' => [
                    'icon'                => 'Значок',
                    'decoration-type'     => 'Тип оформления',
                    'chaining-type'       => 'Тип связывания',
                    'suggest'             => 'Предлагать',
                    'trigger'             => 'Запускать',
                ],
            ],

            'status-and-configuration-information' => [
                'title' => 'Статус и настройки',

                'fields' => [
                    'status'               => 'Статус',
                    'keep-done-activities' => 'Сохранять выполненные действия',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'       => 'Тип действия',
            'summary'    => 'Краткое описание',
            'planned-in' => 'Запланировано через',
            'type'       => 'Тип',
            'action'     => 'Действие',
            'status'     => 'Статус',
            'created-at' => 'Создано',
            'updated-at' => 'Обновлено',
        ],

        'groups' => [
            'name'             => 'Название',
            'action-category'  => 'Категория действия',
            'status'           => 'Статус',
            'delay-count'      => 'Величина задержки',
            'delay-unit'       => 'Единица задержки',
            'delay-source'     => 'Источник задержки',
            'associated-model' => 'Связанная модель',
            'chaining-type'    => 'Тип связывания',
            'decoration-type'  => 'Тип оформления',
            'default-user'     => 'Пользователь по умолчанию',
            'creation-date'    => 'Дата создания',
            'last-update'      => 'Последнее обновление',
        ],

        'filters' => [
            'action'    => 'Действие',
            'status'    => 'Статус',
            'has-delay' => 'Есть задержка',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Тип действия восстановлен',
                    'body'  => 'Тип действия успешно восстановлен.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Тип действия удалён',
                    'body'  => 'Тип действия успешно удалён.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Тип действия удалён безвозвратно',
                        'body'  => 'Тип действия успешно удалён безвозвратно.',
                    ],
                    'error' => [
                        'title' => 'Не удалось удалить тип действия',
                        'body'  => 'Тип действия нельзя удалить, так как он сейчас используется.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Типы действий восстановлены',
                    'body'  => 'Типы действий успешно восстановлены.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Типы действий удалены',
                    'body'  => 'Типы действий успешно удалены.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Типы действий удалены безвозвратно',
                    'body'  => 'Типы действий успешно удалены безвозвратно.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'activity-type-details' => [
                'title' => 'Общие сведения',

                'entries' => [
                    'name'                => 'Тип действия',
                    'name-tooltip'        => 'Введите официальное название типа действия',
                    'action'              => 'Действие',
                    'default-user'        => 'Пользователь по умолчанию',
                    'plugin'              => 'Плагин',
                    'summary'             => 'Краткое описание',
                    'note'                => 'Примечание',
                ],
            ],

            'delay-information' => [
                'title' => 'Задержка',

                'entries' => [
                    'delay-count'            => 'Величина задержки',
                    'delay-unit'             => 'Единица задержки',
                    'delay-form'             => 'Отсчёт задержки от',
                    'delay-form-helper-text' => 'От чего отсчитывается задержка',
                ],
            ],

            'advanced-information' => [
                'title' => 'Дополнительно',

                'entries' => [
                    'icon'                => 'Значок',
                    'decoration-type'     => 'Тип оформления',
                    'chaining-type'       => 'Тип связывания',
                    'suggest'             => 'Предлагать',
                    'trigger'             => 'Запускать',
                ],
            ],

            'status-and-configuration-information' => [
                'title' => 'Статус и настройки',

                'entries' => [
                    'status'               => 'Статус',
                    'keep-done-activities' => 'Сохранять выполненные действия',
                ],
            ],
        ],
    ],
];
