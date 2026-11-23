<?php

return [
    'navigation' => [
        'title' => 'Упаковка Types',
        'group' => 'Доставка',
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
                    'max-weight' => 'Max Вес',

                    'fieldsets' => [
                        'size' => [
                            'title' => 'Размер',

                            'fields' => [
                                'length' => 'Length',
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
            'max-weight' => 'Max Вес',
            'width'      => 'Ширина',
            'height'     => 'Высота',
            'length'     => 'Length',
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
                    'title' => 'Упаковка Тип удалено',
                    'body'  => 'упаковка тип есть удалено successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Упаковка Тип удалено',
                    'body'  => 'упаковка тип есть удалено successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'   => 'Общее Сведения',
                'entries' => [
                    'name'      => 'Название',
                    'fieldsets' => [
                        'size' => [
                            'title'   => 'Упаковка Dimensions',
                            'entries' => [
                                'length' => 'Length',
                                'width'  => 'Ширина',
                                'height' => 'Высота',
                            ],
                        ],
                    ],
                    'weight'     => 'Base Вес',
                    'max-weight' => 'Maximum Вес',
                    'barcode'    => 'Штрихкод',
                    'company'    => 'Компания',
                    'created-at' => 'Создано',
                    'updated-at' => 'Последний Обновлено',
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
