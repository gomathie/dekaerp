<?php

return [
    'navigation' => [
        'group' => 'Обслуживание',
        'title' => 'Заявки на обслуживание',
    ],

    'form' => [
        'sections' => [
            'request' => [
                'title'  => 'Заявка',
                'fields' => [
                    'name'                      => 'Заявка',
                    'name-placeholder'          => 'например, не работает экран',
                    'equipment'                 => 'Оборудование',
                    'category'                  => 'Категория',
                    'requested-at'              => 'Дата заявки',
                    'requested-at-hint-tooltip' => 'Дата подачи заявки на обслуживание.',
                    'maintenance-type'          => 'Тип обслуживания',
                    'recurrent'                 => 'Повторяющееся',
                    'repeat-every'              => 'Повторять каждые',
                    'maintenance-type-options'  => [
                        'corrective' => 'Корректирующее',
                        'preventive' => 'Профилактическое',
                    ],
                ],
                'tabs' => [
                    'notes' => [
                        'title'  => 'Примечания',
                        'fields' => [
                            'description'             => 'Внутренние примечания',
                            'description-placeholder' => 'Внутренние примечания',
                        ],
                    ],
                    'instructions' => [
                        'title'  => 'Инструкции',
                        'fields' => [
                            'instruction-type'         => 'Тип инструкции',
                            'instruction-type-options' => [
                                'pdf'          => 'PDF',
                                'google-slide' => 'Google Slides',
                                'text'         => 'Текст',
                            ],
                            'instruction-pdf'              => 'PDF',
                            'instruction-google-slide'     => 'Google Slides',
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
                    'responsible'               => 'Ответственный',
                    'scheduled-at'              => 'Плановая дата',
                    'scheduled-at-hint-tooltip' => 'Дата и время планового начала работ по обслуживанию.',
                    'duration'                  => 'Длительность',
                    'duration-hint-tooltip'     => 'Ожидаемая длительность обслуживания.',
                    'duration-suffix'           => 'ч',
                    'priority'                  => 'Приоритет',
                    'company'                   => 'Компания',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'       => 'Темы',
            'creator'    => 'Создал пользователь',
            'technician' => 'Техник',
            'category'   => 'Категория',
            'stage'      => 'Этап',
            'company'    => 'Компания',
        ],

        'groups' => [
            'stage'       => 'Этап',
            'assigned-to' => 'Исполнитель',
            'category'    => 'Категория',
            'created-by'  => 'Создал',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Заявка на обслуживание восстановлена',
                    'body'  => 'Заявка на обслуживание успешно восстановлена.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Заявка на обслуживание архивирована',
                    'body'  => 'Заявка на обслуживание успешно архивирована.',
                ],
            ],
            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Заявка на обслуживание удалена',
                        'body'  => 'Заявка на обслуживание удалена безвозвратно.',
                    ],
                    'error' => [
                        'title' => 'Не удалось удалить заявку на обслуживание',
                        'body'  => 'На эту заявку на обслуживание ссылается другая запись.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Заявки на обслуживание восстановлены',
                    'body'  => 'Выбранные заявки на обслуживание успешно восстановлены.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Заявки на обслуживание архивированы',
                    'body'  => 'Выбранные заявки на обслуживание успешно архивированы.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'request' => [
                'title'   => 'Заявка',
                'entries' => [
                    'name'                     => 'Заявка',
                    'equipment'                => 'Оборудование',
                    'category'                 => 'Категория',
                    'requested-at'             => 'Дата заявки',
                    'maintenance-type'         => 'Тип обслуживания',
                    'instruction-type'         => 'Тип инструкции',
                    'instruction-pdf'          => 'PDF',
                    'instruction-google-slide' => 'Google Slides',
                    'description'              => 'Внутренние примечания',
                    'instruction-text'         => 'Описание',
                ],
            ],

            'settings' => [
                'title'   => 'Настройки',
                'entries' => [
                    'team'            => 'Команда',
                    'responsible'     => 'Ответственный',
                    'scheduled-at'    => 'Плановая дата',
                    'duration'        => 'Длительность',
                    'duration-suffix' => 'ч',
                    'priority'        => 'Приоритет',
                    'company'         => 'Компания',
                ],
            ],
        ],
    ],
];
