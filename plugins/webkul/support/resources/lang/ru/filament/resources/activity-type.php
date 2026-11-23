<?php

return [
    'title' => 'Отделы',

    'navigation' => [
        'title' => 'Отделы',
        'group' => 'Сотрудники',
    ],

    'form' => [
        'sections' => [
            'activity-type-details' => [
                'title' => 'Общее Сведения',

                'fields' => [
                    'name'                => 'Активность Тип',
                    'name-tooltip'        => 'Введите official активность тип название',
                    'action'              => 'Действие',
                    'default-user'        => 'По умолчанию Пользователь',
                    'summary'             => 'Сводка',
                    'note'                => 'Заметка',
                ],
            ],

            'delay-information' => [
                'title' => 'Delay Сведения',

                'fields' => [
                    'delay-count'            => 'Delay Количество',
                    'delay-unit'             => 'Delay Единица',
                    'delay-form'             => 'Delay Форма',
                    'delay-form-helper-text' => 'Источник delay calculation',
                ],
            ],

            'advanced-information' => [
                'title' => 'Advanced Сведения',

                'fields' => [
                    'icon'                => 'Значок',
                    'decoration-type'     => 'Decoration Тип',
                    'chaining-type'       => 'Chaining Тип',
                    'suggest'             => 'Suggest',
                    'trigger'             => 'Trigger',
                ],
            ],

            'status-and-configuration-information' => [
                'title' => 'Статус & Конфигурация',

                'fields' => [
                    'status'               => 'Статус',
                    'keep-done-activities' => 'Оставить Завершён Деятельность',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'       => 'Активность Тип',
            'summary'    => 'Сводка',
            'planned-in' => 'Запланировано В',
            'type'       => 'Тип',
            'action'     => 'Действие',
            'status'     => 'Статус',
            'created-at' => 'Создано',
            'updated-at' => 'Обновлено',
        ],

        'groups' => [
            'name'             => 'Название',
            'action-category'  => 'Действие Категория',
            'status'           => 'Статус',
            'delay-count'      => 'Delay Количество',
            'delay-unit'       => 'Delay Единица',
            'delay-source'     => 'Delay Источник',
            'associated-model' => 'Associated Model',
            'chaining-type'    => 'Chaining Тип',
            'decoration-type'  => 'Decoration Тип',
            'default-user'     => 'По умолчанию Пользователь',
            'creation-date'    => 'Creation Дата',
            'last-update'      => 'Последний Обновить',
        ],

        'filters' => [
            'action'    => 'Действие',
            'status'    => 'Статус',
            'has-delay' => 'Есть Delay',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Активность тип restored',
                    'body'  => 'активность тип есть restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Активность тип удалено',
                    'body'  => 'активность тип есть удалено successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Активность тип force удалено',
                        'body'  => 'Активность тип есть force удалено successfully.',
                    ],
                    'error' => [
                        'title' => 'Активность тип could не быть удалено',
                        'body'  => 'Активность тип нельзя быть удалено потому что это currently в использовать.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Активность types restored',
                    'body'  => 'активность types есть restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Активность types удалено',
                    'body'  => 'активность types есть удалено successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Активность types force удалено',
                    'body'  => 'активность types есть force удалено successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'activity-type-details' => [
                'title' => 'Общее Сведения',

                'entries' => [
                    'name'                => 'Активность Тип',
                    'name-tooltip'        => 'Введите official активность тип название',
                    'action'              => 'Действие',
                    'default-user'        => 'По умолчанию Пользователь',
                    'plugin'              => 'Модуль',
                    'summary'             => 'Сводка',
                    'note'                => 'Заметка',
                ],
            ],

            'delay-information' => [
                'title' => 'Delay Сведения',

                'entries' => [
                    'delay-count'            => 'Delay Количество',
                    'delay-unit'             => 'Delay Единица',
                    'delay-form'             => 'Delay Форма',
                    'delay-form-helper-text' => 'Источник delay calculation',
                ],
            ],

            'advanced-information' => [
                'title' => 'Advanced Сведения',

                'entries' => [
                    'icon'                => 'Значок',
                    'decoration-type'     => 'Decoration Тип',
                    'chaining-type'       => 'Chaining Тип',
                    'suggest'             => 'Suggest',
                    'trigger'             => 'Trigger',
                ],
            ],

            'status-and-configuration-information' => [
                'title' => 'Статус & Конфигурация',

                'entries' => [
                    'status'               => 'Статус',
                    'keep-done-activities' => 'Оставить Завершён Деятельность',
                ],
            ],
        ],
    ],
];
