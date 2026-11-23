<?php

return [
    'navigation' => [
        'title' => 'Work Centers',
        'group' => 'Конфигурация',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Общее',
                'fields' => [
                    'name'                     => 'Название',
                    'name-placeholder'         => 'eg. Assembly Строка 1',
                    'code'                     => 'Код',
                    'code-placeholder'         => 'eg. AL1',
                    'working-state'            => 'Working Состояние',
                    'color'                    => 'Цвет',
                    'tags'                     => 'Метка',
                    'alternative-work-centers' => 'Alternative Work Centers',
                    'company'                  => 'Компания',
                    'calendar'                 => 'Working Часы',
                ],
            ],

            'information' => [
                'title'     => 'Общее Сведения',
                'fieldsets' => [
                    'production-information' => 'Рабочая среда Сведения',
                    'costing-information'    => 'Costing Сведения',
                ],
                'fields' => [
                    'default-capacity' => 'По умолчанию Capacity',
                    'time-efficiency'  => 'Time Efficiency',
                    'oee-target'       => 'OEE Назначение',
                    'costs-per-hour'   => 'Стоимость за Час',
                    'cost-suffix'      => 'за час',
                    'setup-time'       => 'Setup Time',
                    'cleanup-time'     => 'Cleanup Time',
                    'time-suffix'      => 'минуты',
                ],
            ],

            'description' => [
                'title'  => 'Описание',
                'fields' => [
                    'note'             => 'Описание',
                    'note-placeholder' => 'Описание work center...',
                ],
            ],

            'specific-capacity' => [
                'title'  => 'Specific Capacity',
                'fields' => [
                    'records' => 'Specific Capacity',
                ],
                'columns' => [
                    'product'      => 'Товар',
                    'product-uom'  => 'UOM',
                    'capacity'     => 'Capacity',
                    'setup-time'   => 'Setup Time',
                    'cleanup-time' => 'Cleanup Time',
                ],
                'actions' => [
                    'add' => 'Добавить строка',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'             => 'Название',
            'code'             => 'Код',
            'company'          => 'Компания',
            'calendar'         => 'Working Часы',
            'working-state'    => 'Working Состояние',
            'default-capacity' => 'Capacity',
            'time-efficiency'  => 'Efficiency',
            'costs-per-hour'   => 'Стоимость за Час',
            'deleted-at'       => 'Удалено',
            'created-at'       => 'Создано',
            'updated-at'       => 'Обновлено',
        ],

        'groups' => [
            'company' => 'Компания',
        ],

        'filters' => [
            'company'       => 'Компания',
            'working-state' => 'Working Состояние',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Work center restored',
                    'body'  => 'work center есть restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Work center в архиве',
                    'body'  => 'work center есть в архиве successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Work center удалено',
                        'body'  => 'work center есть permanently удалено.',
                    ],

                    'error' => [
                        'title' => 'Work center could не быть удалено',
                        'body'  => 'work center нельзя быть удалено потому что это currently в использовать.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Work centers restored',
                    'body'  => 'выбрано work centers есть restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Work centers в архиве',
                    'body'  => 'выбрано work centers есть в архиве successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Work centers удалено',
                        'body'  => 'выбрано work centers есть permanently удалено.',
                    ],

                    'error' => [
                        'title' => 'Work centers could не быть удалено',
                        'body'  => 'One или больше выбрано work centers currently в использовать.',
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
                    'name'                     => 'Work Center Название',
                    'code'                     => 'Код',
                    'working-state'            => 'Working Состояние',
                    'tags'                     => 'Метка',
                    'alternative-work-centers' => 'Alternative Work Centers',
                    'company'                  => 'Компания',
                    'calendar'                 => 'Working Часы',
                ],
            ],

            'information' => [
                'title'     => 'Общее Сведения',
                'fieldsets' => [
                    'production-information' => 'Рабочая среда Сведения',
                    'costing-information'    => 'Costing Сведения',
                ],

                'entries' => [
                    'default-capacity' => 'По умолчанию Capacity',
                    'time-efficiency'  => 'Time Efficiency',
                    'oee-target'       => 'OEE Назначение',
                    'costs-per-hour'   => 'Стоимость за Час',
                    'cost-suffix'      => 'за work center',
                    'setup-time'       => 'Setup Time',
                    'cleanup-time'     => 'Cleanup Time',
                    'time-suffix'      => 'минуты',
                ],
            ],

            'description' => [
                'title'   => 'Описание',
                'entries' => [
                    'note' => 'Описание',
                ],
            ],

            'specific-capacity' => [
                'title'   => 'Specific Capacities',
                'columns' => [
                    'product'      => 'Товар',
                    'product-uom'  => 'UOM',
                    'capacity'     => 'Capacity',
                    'setup-time'   => 'Setup Time',
                    'cleanup-time' => 'Cleanup Time',
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
