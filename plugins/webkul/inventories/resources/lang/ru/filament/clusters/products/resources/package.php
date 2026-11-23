<?php

return [
    'navigation' => [
        'title' => 'Упаковки',
        'group' => 'Склад',
    ],

    'global-search' => [
        'name'         => 'Название',
        'package-type' => 'Упаковка Тип',
        'location'     => 'Место',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Общее',

                'fields' => [
                    'name'             => 'Название',
                    'name-placeholder' => 'eg. PACK007',
                    'package-type'     => 'Упаковка Тип',
                    'pack-date'        => 'Pack Дата',
                    'location'         => 'Место',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'         => 'Название',
            'package-type' => 'Упаковка Тип',
            'location'     => 'Место',
            'company'      => 'Компания',
            'created-at'   => 'Создано',
            'updated-at'   => 'Обновлено',
        ],

        'groups' => [
            'package-type'   => 'Упаковка Тип',
            'location'       => 'Место',
            'created-at'     => 'Создано',
        ],

        'filters' => [
            'package-type' => 'Упаковка Тип',
            'location'     => 'Место',
            'creator'      => 'Creator',
            'company'      => 'Компания',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Упаковка удалено',
                        'body'  => 'упаковка есть удалено successfully.',
                    ],

                    'error' => [
                        'title' => 'Упаковка could не быть удалено',
                        'body'  => 'упаковка нельзя быть удалено потому что это currently в использовать.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'print-without-content' => [
                'label' => 'Печать Штрихкод',
            ],

            'print-with-content' => [
                'label' => 'Печать Штрихкод С Content',
            ],

            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Упаковки удалено',
                        'body'  => 'упаковки есть удалено successfully.',
                    ],

                    'error' => [
                        'title' => 'Упаковки could не быть удалено',
                        'body'  => 'упаковки нельзя быть удалено потому что они currently в использовать.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Упаковка Подробности',

                'entries' => [
                    'name'         => 'Упаковка Название',
                    'package-type' => 'Упаковка Тип',
                    'pack-date'    => 'Pack Дата',
                    'location'     => 'Место',
                    'company'      => 'Компания',
                    'created-at'   => 'Создано',
                    'updated-at'   => 'Последний Обновлено',
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
