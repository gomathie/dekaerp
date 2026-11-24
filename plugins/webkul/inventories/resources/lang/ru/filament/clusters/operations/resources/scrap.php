<?php

return [
    'navigation' => [
        'title' => 'Списания',
        'group' => 'Корректировки',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Общее',

                'fields' => [
                    'product'              => 'Товар',
                    'package'              => 'Упаковка',
                    'quantity'             => 'Количество',
                    'unit'                 => 'Единица измерения',
                    'lot'                  => 'Партия/серийный номер',
                    'tags'                 => 'Теги',
                    'name'                 => 'Название',
                    'color'                => 'Цвет',
                    'owner'                => 'Владелец',
                    'source-location'      => 'Исходное местоположение',
                    'destination-location' => 'Местоположение списания',
                    'source-document'      => 'Исходный документ',
                    'company'              => 'Компания',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'date'            => 'Дата',
            'reference'       => 'Номер',
            'product'         => 'Товар',
            'package'         => 'Упаковка',
            'quantity'        => 'Количество',
            'uom'             => 'Единица измерения',
            'source-location' => 'Исходное местоположение',
            'scrap-location'  => 'Местоположение списания',
            'unit'            => 'Единица измерения',
            'lot'             => 'Партия/серийный номер',
            'tags'            => 'Теги',
            'state'           => 'Статус',
        ],

        'groups' => [
            'product'              => 'Товар',
            'source-location'      => 'Исходное местоположение',
            'destination-location' => 'Местоположение списания',
        ],

        'filters' => [
            'source-location'      => 'Исходное местоположение',
            'destination-location' => 'Местоположение списания',
            'product'              => 'Товар',
            'state'                => 'Статус',
            'product-category'     => 'Категория товаров',
            'uom'                  => 'Единица измерения',
            'lot'                  => 'Партия/серийный номер',
            'package'              => 'Упаковка',
            'tags'                 => 'Теги',
            'company'              => 'Компания',
            'quantity'             => 'Количество',
            'creator'              => 'Автор',
            'closed-at'            => 'Закрыто',
            'created-at'           => 'Создано',
            'updated-at'           => 'Обновлено',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Списание удалено',
                        'body'  => 'Списание успешно удалено.',
                    ],

                    'error' => [
                        'title' => 'Не удалось удалить списание',
                        'body'  => 'Списание нельзя удалить, так как оно используется.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Списания удалены',
                        'body'  => 'Выбранные списания успешно удалены.',
                    ],

                    'error' => [
                        'title' => 'Не удалось удалить списания',
                        'body'  => 'Списания нельзя удалить, так как они используются.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Сведения о списании',

                'entries' => [
                    'product'              => 'Товар',
                    'quantity'             => 'Количество',
                    'lot'                  => 'Партия',
                    'tags'                 => 'Теги',
                    'package'              => 'Упаковка',
                    'owner'                => 'Владелец',
                    'source-location'      => 'Исходное местоположение',
                    'destination-location' => 'Местоположение списания',
                    'source-document'      => 'Исходный документ',
                    'company'              => 'Компания',
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
