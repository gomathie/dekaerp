<?php

return [
    'navigation' => [
        'group' => 'Обслуживание',
        'title' => 'Обслуживание Requests',
    ],

    'form' => [
        'sections' => [
            'request' => [
                'title'  => 'Request',
                'fields' => [
                    'name'                      => 'Request',
                    'name-placeholder'          => 'e.g. Screen не working',
                    'equipment'                 => 'Equipment',
                    'category'                  => 'Категория',
                    'requested-at'              => 'Request Дата',
                    'requested-at-hint-tooltip' => 'дата обслуживание request был reported.',
                    'maintenance-type'          => 'Обслуживание Тип',
                    'recurrent'                 => 'Recurrent',
                    'repeat-every'              => 'Repeat Каждый',
                    'maintenance-type-options'  => [
                        'corrective' => 'Corrective',
                        'preventive' => 'Preventive',
                    ],
                ],
                'tabs' => [
                    'notes' => [
                        'title'  => 'Заметки',
                        'fields' => [
                            'description'             => 'Внутренний Заметки',
                            'description-placeholder' => 'Внутренний заметки',
                        ],
                    ],
                    'instructions' => [
                        'title'  => 'Instructions',
                        'fields' => [
                            'instruction-type'         => 'Instruction Тип',
                            'instruction-type-options' => [
                                'pdf'          => 'PDF',
                                'google-slide' => 'Google Slide',
                                'text'         => 'Text',
                            ],
                            'instruction-pdf'              => 'PDF',
                            'instruction-google-slide'     => 'Google Slide',
                            'instruction-text'             => 'Описание',
                            'instruction-text-placeholder' => 'Описание',
                        ],
                    ],
                ],
            ],

            'settings' => [
                'title'  => 'Настройки',
                'fields' => [
                    'team'                      => 'Команда',
                    'responsible'               => 'Responsible',
                    'scheduled-at'              => 'Scheduled Дата',
                    'scheduled-at-hint-tooltip' => 'дата и time этот обслуживание work запланировано до начать.',
                    'duration'                  => 'Duration',
                    'duration-hint-tooltip'     => 'Ожидается обслуживание duration.',
                    'duration-suffix'           => 'часы',
                    'priority'                  => 'Приоритет',
                    'company'                   => 'Компания',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'       => 'Subjects',
            'creator'    => 'Создано от Пользователь',
            'technician' => 'Technician',
            'category'   => 'Категория',
            'stage'      => 'Этап',
            'company'    => 'Компания',
        ],

        'groups' => [
            'stage'       => 'Этап',
            'assigned-to' => 'Назначен до',
            'category'    => 'Категория',
            'created-by'  => 'Создано От',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Обслуживание request restored',
                    'body'  => 'обслуживание request есть restored successfully.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Обслуживание request в архиве',
                    'body'  => 'обслуживание request есть в архиве successfully.',
                ],
            ],
            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Обслуживание request удалено',
                        'body'  => 'обслуживание request есть permanently удалено.',
                    ],
                    'error' => [
                        'title' => 'Обслуживание request could не быть удалено',
                        'body'  => 'Этот обслуживание request referenced от another запись.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Обслуживание requests restored',
                    'body'  => 'выбрано обслуживание requests есть restored successfully.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Обслуживание requests в архиве',
                    'body'  => 'выбрано обслуживание requests есть в архиве successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'request' => [
                'title'   => 'Request',
                'entries' => [
                    'name'                     => 'Request',
                    'equipment'                => 'Equipment',
                    'category'                 => 'Категория',
                    'requested-at'             => 'Request Дата',
                    'maintenance-type'         => 'Обслуживание Тип',
                    'instruction-type'         => 'Instruction Тип',
                    'instruction-pdf'          => 'PDF',
                    'instruction-google-slide' => 'Google Slide',
                    'description'              => 'Внутренний Заметки',
                    'instruction-text'         => 'Описание',
                ],
            ],

            'settings' => [
                'title'   => 'Настройки',
                'entries' => [
                    'team'            => 'Команда',
                    'responsible'     => 'Responsible',
                    'scheduled-at'    => 'Scheduled Дата',
                    'duration'        => 'Duration',
                    'duration-suffix' => 'часы',
                    'priority'        => 'Приоритет',
                    'company'         => 'Компания',
                ],
            ],
        ],
    ],
];
