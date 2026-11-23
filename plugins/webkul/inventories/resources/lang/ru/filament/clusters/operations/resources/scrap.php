<?php

return [
    'navigation' => [
        'title' => 'Scraps',
        'group' => 'Adjustments',
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
                    'lot'                  => 'Партия/Серийный номер',
                    'tags'                 => 'Метки',
                    'name'                 => 'Название',
                    'color'                => 'Цвет',
                    'owner'                => 'Владелец',
                    'source-location'      => 'Источник Место',
                    'destination-location' => 'Scrap Место',
                    'source-document'      => 'Источник Документ',
                    'company'              => 'Компания',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'date'            => 'Дата',
            'reference'       => 'Ссылка',
            'product'         => 'Товар',
            'package'         => 'Упаковка',
            'quantity'        => 'Количество',
            'uom'             => 'Единица измерения',
            'source-location' => 'Источник Место',
            'scrap-location'  => 'Scrap Место',
            'unit'            => 'Единица измерения',
            'lot'             => 'Партия/Серийный номер',
            'tags'            => 'Метки',
            'state'           => 'Состояние',
        ],

        'groups' => [
            'product'              => 'Товар',
            'source-location'      => 'Источник Место',
            'destination-location' => 'Scrap Место',
        ],

        'filters' => [
            'source-location'      => 'Источник Место',
            'destination-location' => 'Scrap Место',
            'product'              => 'Товар',
            'state'                => 'Состояние',
            'product-category'     => 'Товар Категория',
            'uom'                  => 'Единица измерения',
            'lot'                  => 'Партия/Серийный номер',
            'package'              => 'Упаковка',
            'tags'                 => 'Метки',
            'company'              => 'Компания',
            'quantity'             => 'Количество',
            'creator'              => 'Creator',
            'closed-at'            => 'Закрыт В',
            'created-at'           => 'Создано',
            'updated-at'           => 'Обновлено',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Scrap удалено',
                        'body'  => 'scrap есть удалено successfully.',
                    ],

                    'error' => [
                        'title' => 'Scrap could не быть удалено',
                        'body'  => 'scrap нельзя быть удалено потому что это currently в использовать.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Scraps удалено',
                        'body'  => 'выбрано scraps есть удалено successfully.',
                    ],

                    'error' => [
                        'title' => 'Scraps could не быть удалено',
                        'body'  => 'scraps нельзя быть удалено потому что они currently в использовать.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Scrap Подробности',

                'entries' => [
                    'product'              => 'Товар',
                    'quantity'             => 'Количество',
                    'lot'                  => 'Партия',
                    'tags'                 => 'Метки',
                    'package'              => 'Упаковка',
                    'owner'                => 'Владелец',
                    'source-location'      => 'Источник Место',
                    'destination-location' => 'Scrap Место',
                    'source-document'      => 'Источник Документ',
                    'company'              => 'Компания',
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
