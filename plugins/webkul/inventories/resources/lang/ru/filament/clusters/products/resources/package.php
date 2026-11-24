<?php

return [
    'navigation' => [
        'title' => 'Упаковки',
        'group' => 'Запасы',
    ],

    'global-search' => [
        'name'         => 'Название',
        'package-type' => 'Тип упаковки',
        'location'     => 'Местоположение',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Общее',

                'fields' => [
                    'name'             => 'Название',
                    'name-placeholder' => 'напр. PACK007',
                    'package-type'     => 'Тип упаковки',
                    'pack-date'        => 'Дата упаковки',
                    'location'         => 'Местоположение',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'         => 'Название',
            'package-type' => 'Тип упаковки',
            'location'     => 'Местоположение',
            'company'      => 'Компания',
            'created-at'   => 'Создано',
            'updated-at'   => 'Обновлено',
        ],

        'groups' => [
            'package-type'   => 'Тип упаковки',
            'location'       => 'Местоположение',
            'created-at'     => 'Создано',
        ],

        'filters' => [
            'package-type' => 'Тип упаковки',
            'location'     => 'Местоположение',
            'creator'      => 'Автор',
            'company'      => 'Компания',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Упаковка удалена',
                        'body'  => 'Упаковка успешно удалена.',
                    ],

                    'error' => [
                        'title' => 'Не удалось удалить упаковку',
                        'body'  => 'Упаковку нельзя удалить, так как она используется.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'print-without-content' => [
                'label' => 'Печать штрихкода',
            ],

            'print-with-content' => [
                'label' => 'Печать штрихкода с содержимым',
            ],

            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Упаковки удалены',
                        'body'  => 'Упаковки успешно удалены.',
                    ],

                    'error' => [
                        'title' => 'Не удалось удалить упаковки',
                        'body'  => 'Упаковки нельзя удалить, так как они используются.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Сведения об упаковке',

                'entries' => [
                    'name'         => 'Название упаковки',
                    'package-type' => 'Тип упаковки',
                    'pack-date'    => 'Дата упаковки',
                    'location'     => 'Местоположение',
                    'company'      => 'Компания',
                    'created-at'   => 'Создано',
                    'updated-at'   => 'Последнее обновление',
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
