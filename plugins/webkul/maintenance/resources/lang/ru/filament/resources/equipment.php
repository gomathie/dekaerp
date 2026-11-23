<?php

return [
    'navigation' => [
        'title' => 'Equipment',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Общее Сведения',
                'fields' => [
                    'name' => 'Название',
                    'note' => 'Описание',
                ],
            ],

            'settings' => [
                'title'  => 'Настройки',
                'fields' => [
                    'category'   => 'Equipment Категория',
                    'team'       => 'Обслуживание Команда',
                    'company'    => 'Компания',
                    'technician' => 'Technician',
                    'owner'      => 'Владелец',
                    'location'   => 'Используется в место',
                ],
            ],

            'product-information' => [
                'title'  => 'Товар Сведения',
                'fields' => [
                    'partner'                     => 'Поставщик',
                    'partner-ref'                 => 'Поставщик Ссылка',
                    'model'                       => 'Model',
                    'serial-no'                   => 'Серийный номер Номер',
                    'effective-date'              => 'Effective Дата',
                    'effective-date-hint-tooltip' => 'Используется как starting point для calculating Mean Time Между Сбой.',
                    'cost'                        => 'Стоимость',
                    'warranty-date'               => 'Warranty Expiration Дата',
                ],
            ],

            'maintenance' => [
                'title'  => 'Обслуживание',
                'fields' => [
                    'expected-mtbf' => 'Ожидается Mean Time Между Сбой',
                ],
                'suffixes' => [
                    'days' => 'дни',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'       => 'Equipment Название',
            'owner'      => 'Владелец',
            'serial-no'  => 'Серийный номер Номер',
            'category'   => 'Equipment Категория',
            'technician' => 'Technician',
            'company'    => 'Компания',
            'created-at' => 'Создано',
        ],

        'filters' => [
            'category'   => 'Equipment Категория',
            'team'       => 'Обслуживание Команда',
            'technician' => 'Technician',
        ],

        'groups' => [
            'category'   => 'Equipment Категория',
            'owner'      => 'Владелец',
            'technician' => 'Technician',
            'vendor'     => 'Поставщик',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Equipment обновлено',
                    'body'  => 'equipment есть обновлено successfully.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Equipment restored',
                    'body'  => 'equipment есть restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Equipment в архиве',
                    'body'  => 'equipment есть в архиве successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Equipment удалено',
                        'body'  => 'equipment есть permanently удалено.',
                    ],

                    'error' => [
                        'title' => 'Equipment could не быть удалено',
                        'body'  => 'Этот equipment referenced от another запись.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Equipment restored',
                    'body'  => 'выбрано equipment есть restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Equipment в архиве',
                    'body'  => 'выбрано equipment есть в архиве successfully.',
                ],
            ],
        ],

        'empty-state' => [
            'create' => [
                'notification' => [
                    'title' => 'Equipment создано',
                    'body'  => 'equipment есть создано successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'   => 'Общее Сведения',
                'entries' => [
                    'name' => 'Название',
                    'note' => 'Описание',
                ],
            ],

            'settings' => [
                'title'   => 'Настройки',
                'entries' => [
                    'category'   => 'Equipment Категория',
                    'team'       => 'Обслуживание Команда',
                    'company'    => 'Компания',
                    'technician' => 'Technician',
                    'owner'      => 'Владелец',
                    'location'   => 'Используется в место',
                ],
            ],

            'product-information' => [
                'title'   => 'Товар Сведения',
                'entries' => [
                    'partner'        => 'Поставщик',
                    'partner-ref'    => 'Поставщик Ссылка',
                    'model'          => 'Model',
                    'serial-no'      => 'Серийный номер Номер',
                    'effective-date' => 'Effective Дата',
                    'cost'           => 'Стоимость',
                    'warranty-date'  => 'Warranty Expiration Дата',
                ],
            ],

            'maintenance' => [
                'title'   => 'Обслуживание',
                'entries' => [
                    'expected-mtbf'          => 'Ожидается Mean Time Между Сбой',
                    'maintenance-count'      => 'Обслуживание Количество',
                    'maintenance-open-count' => 'Открыт Обслуживание Количество',
                    'assigned-at'            => 'Назначен Дата',
                    'scraped-at'             => 'Scrap Дата',
                ],
                'suffixes' => [
                    'days' => 'дни',
                ],
            ],
        ],
    ],
];
