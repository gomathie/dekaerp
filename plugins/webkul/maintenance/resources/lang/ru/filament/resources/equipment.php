<?php

return [
    'navigation' => [
        'title' => 'Оборудование',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Общие сведения',
                'fields' => [
                    'name' => 'Название',
                    'note' => 'Описание',
                ],
            ],

            'settings' => [
                'title'  => 'Настройки',
                'fields' => [
                    'category'   => 'Категория оборудования',
                    'team'       => 'Команда обслуживания',
                    'company'    => 'Компания',
                    'technician' => 'Техник',
                    'owner'      => 'Владелец',
                    'location'   => 'Место использования',
                ],
            ],

            'product-information' => [
                'title'  => 'Сведения о товаре',
                'fields' => [
                    'partner'                     => 'Поставщик',
                    'partner-ref'                 => 'Артикул поставщика',
                    'model'                       => 'Модель',
                    'serial-no'                   => 'Серийный номер',
                    'effective-date'              => 'Дата ввода в эксплуатацию',
                    'effective-date-hint-tooltip' => 'Точка отсчёта для расчёта среднего времени между отказами.',
                    'cost'                        => 'Стоимость',
                    'warranty-date'               => 'Окончание гарантии',
                ],
            ],

            'maintenance' => [
                'title'  => 'Обслуживание',
                'fields' => [
                    'expected-mtbf' => 'Ожидаемое среднее время между отказами',
                ],
                'suffixes' => [
                    'days' => 'дн.',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'       => 'Название оборудования',
            'owner'      => 'Владелец',
            'serial-no'  => 'Серийный номер',
            'category'   => 'Категория оборудования',
            'technician' => 'Техник',
            'company'    => 'Компания',
            'created-at' => 'Создано',
        ],

        'filters' => [
            'category'   => 'Категория оборудования',
            'team'       => 'Команда обслуживания',
            'technician' => 'Техник',
        ],

        'groups' => [
            'category'   => 'Категория оборудования',
            'owner'      => 'Владелец',
            'technician' => 'Техник',
            'vendor'     => 'Поставщик',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Оборудование обновлено',
                    'body'  => 'Оборудование успешно обновлено.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Оборудование восстановлено',
                    'body'  => 'Оборудование успешно восстановлено.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Оборудование архивировано',
                    'body'  => 'Оборудование успешно архивировано.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Оборудование удалено',
                        'body'  => 'Оборудование удалено безвозвратно.',
                    ],

                    'error' => [
                        'title' => 'Не удалось удалить оборудование',
                        'body'  => 'На это оборудование ссылается другая запись.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Оборудование восстановлено',
                    'body'  => 'Выбранное оборудование успешно восстановлено.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Оборудование архивировано',
                    'body'  => 'Выбранное оборудование успешно архивировано.',
                ],
            ],
        ],

        'empty-state' => [
            'create' => [
                'notification' => [
                    'title' => 'Оборудование создано',
                    'body'  => 'Оборудование успешно создано.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'   => 'Общие сведения',
                'entries' => [
                    'name' => 'Название',
                    'note' => 'Описание',
                ],
            ],

            'settings' => [
                'title'   => 'Настройки',
                'entries' => [
                    'category'   => 'Категория оборудования',
                    'team'       => 'Команда обслуживания',
                    'company'    => 'Компания',
                    'technician' => 'Техник',
                    'owner'      => 'Владелец',
                    'location'   => 'Место использования',
                ],
            ],

            'product-information' => [
                'title'   => 'Сведения о товаре',
                'entries' => [
                    'partner'        => 'Поставщик',
                    'partner-ref'    => 'Артикул поставщика',
                    'model'          => 'Модель',
                    'serial-no'      => 'Серийный номер',
                    'effective-date' => 'Дата ввода в эксплуатацию',
                    'cost'           => 'Стоимость',
                    'warranty-date'  => 'Окончание гарантии',
                ],
            ],

            'maintenance' => [
                'title'   => 'Обслуживание',
                'entries' => [
                    'expected-mtbf'          => 'Ожидаемое среднее время между отказами',
                    'maintenance-count'      => 'Число обслуживаний',
                    'maintenance-open-count' => 'Открытые обслуживания',
                    'assigned-at'            => 'Дата назначения',
                    'scraped-at'             => 'Дата списания',
                ],
                'suffixes' => [
                    'days' => 'дн.',
                ],
            ],
        ],
    ],
];
