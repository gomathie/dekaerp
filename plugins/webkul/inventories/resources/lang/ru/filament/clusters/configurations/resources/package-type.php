<?php

return [
    'navigation' => [
        'title' => 'Типы упаковки',
        'group' => 'Отгрузка',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Общее',

                'fields' => [
                    'name'       => 'Название',
                    'barcode'    => 'Штрихкод',
                    'company'    => 'Компания',
                    'weight'     => 'Вес',
                    'max-weight' => 'Макс. вес',

                    'fieldsets' => [
                        'size' => [
                            'title' => 'Размер',

                            'fields' => [
                                'length' => 'Длина',
                                'width'  => 'Ширина',
                                'height' => 'Высота',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'       => 'Название',
            'barcode'    => 'Штрихкод',
            'weight'     => 'Вес',
            'max-weight' => 'Макс. вес',
            'width'      => 'Ширина',
            'height'     => 'Высота',
            'length'     => 'Длина',
            'created-at' => 'Создано',
            'updated-at' => 'Обновлено',
        ],

        'groups' => [
            'created-at' => 'Создано',
            'updated-at' => 'Обновлено',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Тип упаковки удалён',
                    'body'  => 'Тип упаковки успешно удалён.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Тип упаковки удалён',
                    'body'  => 'Тип упаковки успешно удалён.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'   => 'Общая информация',
                'entries' => [
                    'name'      => 'Название',
                    'fieldsets' => [
                        'size' => [
                            'title'   => 'Размеры упаковки',
                            'entries' => [
                                'length' => 'Длина',
                                'width'  => 'Ширина',
                                'height' => 'Высота',
                            ],
                        ],
                    ],
                    'weight'     => 'Собственный вес',
                    'max-weight' => 'Максимальный вес',
                    'barcode'    => 'Штрихкод',
                    'company'    => 'Компания',
                    'created-at' => 'Создано',
                    'updated-at' => 'Последнее обновление',
                ],
            ],

            'record-information' => [
                'title' => 'Сведения о записи',

                'entries' => [
                    'created-by'   => 'Создал',
                    'created-at'   => 'Создано',
                    'last-updated' => 'Последнее обновление',
                ],
            ],
        ],
    ],
];
