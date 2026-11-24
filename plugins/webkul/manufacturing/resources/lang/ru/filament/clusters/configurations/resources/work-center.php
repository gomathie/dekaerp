<?php

return [
    'navigation' => [
        'title' => 'Рабочие центры',
        'group' => 'Конфигурация',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Общее',
                'fields' => [
                    'name'                     => 'Название',
                    'name-placeholder'         => 'напр. Сборочная линия 1',
                    'code'                     => 'Код',
                    'code-placeholder'         => 'напр. AL1',
                    'working-state'            => 'Рабочее состояние',
                    'color'                    => 'Цвет',
                    'tags'                     => 'Тег',
                    'alternative-work-centers' => 'Альтернативные рабочие центры',
                    'company'                  => 'Компания',
                    'calendar'                 => 'Рабочие часы',
                ],
            ],

            'information' => [
                'title'     => 'Общая информация',
                'fieldsets' => [
                    'production-information' => 'Производственные данные',
                    'costing-information'    => 'Данные о затратах',
                ],
                'fields' => [
                    'default-capacity' => 'Мощность по умолчанию',
                    'time-efficiency'  => 'Эффективность по времени',
                    'oee-target'       => 'Целевой OEE',
                    'costs-per-hour'   => 'Стоимость часа',
                    'cost-suffix'      => 'в час',
                    'setup-time'       => 'Время наладки',
                    'cleanup-time'     => 'Время уборки',
                    'time-suffix'      => 'минут',
                ],
            ],

            'description' => [
                'title'  => 'Описание',
                'fields' => [
                    'note'             => 'Описание',
                    'note-placeholder' => 'Описание рабочего центра…',
                ],
            ],

            'specific-capacity' => [
                'title'  => 'Особая мощность',
                'fields' => [
                    'records' => 'Особая мощность',
                ],
                'columns' => [
                    'product'      => 'Товар',
                    'product-uom'  => 'Ед. изм.',
                    'capacity'     => 'Мощность',
                    'setup-time'   => 'Время наладки',
                    'cleanup-time' => 'Время уборки',
                ],
                'actions' => [
                    'add' => 'Добавить строку',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'             => 'Название',
            'code'             => 'Код',
            'company'          => 'Компания',
            'calendar'         => 'Рабочие часы',
            'working-state'    => 'Рабочее состояние',
            'default-capacity' => 'Мощность',
            'time-efficiency'  => 'Эффективность',
            'costs-per-hour'   => 'Стоимость часа',
            'deleted-at'       => 'Удалён',
            'created-at'       => 'Создан',
            'updated-at'       => 'Обновлён',
        ],

        'groups' => [
            'company' => 'Компания',
        ],

        'filters' => [
            'company'       => 'Компания',
            'working-state' => 'Рабочее состояние',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Рабочий центр восстановлен',
                    'body'  => 'Рабочий центр успешно восстановлен.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Рабочий центр архивирован',
                    'body'  => 'Рабочий центр успешно архивирован.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Рабочий центр удалён',
                        'body'  => 'Рабочий центр удалён безвозвратно.',
                    ],

                    'error' => [
                        'title' => 'Не удалось удалить рабочий центр',
                        'body'  => 'Рабочий центр нельзя удалить, так как он используется.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Рабочие центры восстановлены',
                    'body'  => 'Выбранные рабочие центры успешно восстановлены.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Рабочие центры архивированы',
                    'body'  => 'Выбранные рабочие центры успешно архивированы.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Рабочие центры удалены',
                        'body'  => 'Выбранные рабочие центры удалены безвозвратно.',
                    ],

                    'error' => [
                        'title' => 'Не удалось удалить рабочие центры',
                        'body'  => 'Один или несколько выбранных рабочих центров используются.',
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
                    'name'                     => 'Название рабочего центра',
                    'code'                     => 'Код',
                    'working-state'            => 'Рабочее состояние',
                    'tags'                     => 'Тег',
                    'alternative-work-centers' => 'Альтернативные рабочие центры',
                    'company'                  => 'Компания',
                    'calendar'                 => 'Рабочие часы',
                ],
            ],

            'information' => [
                'title'     => 'Общая информация',
                'fieldsets' => [
                    'production-information' => 'Производственные данные',
                    'costing-information'    => 'Данные о затратах',
                ],

                'entries' => [
                    'default-capacity' => 'Мощность по умолчанию',
                    'time-efficiency'  => 'Эффективность по времени',
                    'oee-target'       => 'Целевой OEE',
                    'costs-per-hour'   => 'Стоимость часа',
                    'cost-suffix'      => 'на рабочий центр',
                    'setup-time'       => 'Время наладки',
                    'cleanup-time'     => 'Время уборки',
                    'time-suffix'      => 'минут',
                ],
            ],

            'description' => [
                'title'   => 'Описание',
                'entries' => [
                    'note' => 'Описание',
                ],
            ],

            'specific-capacity' => [
                'title'   => 'Особые мощности',
                'columns' => [
                    'product'      => 'Товар',
                    'product-uom'  => 'Ед. изм.',
                    'capacity'     => 'Мощность',
                    'setup-time'   => 'Время наладки',
                    'cleanup-time' => 'Время уборки',
                ],
            ],

            'record-information' => [
                'title' => 'Сведения о записи',

                'entries' => [
                    'created-by'   => 'Автор',
                    'created-at'   => 'Создан',
                    'last-updated' => 'Последнее обновление',
                ],
            ],
        ],
    ],
];
