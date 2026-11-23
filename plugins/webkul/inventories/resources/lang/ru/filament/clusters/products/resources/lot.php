<?php

return [
    'navigation' => [
        'title' => 'Партии / Серийный номер Numbers',
        'group' => 'Склад',
    ],

    'global-search' => [
        'ref'     => 'Ссылка',
        'product' => 'Товар',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Общее',

                'fields' => [
                    'name'                   => 'Название',
                    'name-placeholder'       => 'e.g. Партия/0001/20121',
                    'product'                => 'Товар',
                    'product-hint-tooltip'   => 'товар associated с этот партия/серийный номер номер. Это нельзя быть изменено если это есть уже moved.',
                    'company'                => 'Компания',
                    'reference'              => 'Ссылка',
                    'reference-hint-tooltip' => 'внутренний ссылка номер, если different от manufacturer\'s партия/серийный номер номер.',
                    'description'            => 'Описание',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'         => 'Название',
            'product'      => 'Товар',
            'on-hand-qty'  => 'Вкл. Вручную Количество',
            'reference'    => 'Внутренний Ссылка',
            'created-at'   => 'Создано',
            'updated-at'   => 'Обновлено',
        ],

        'groups' => [
            'product'        => 'Товар',
            'location'       => 'Место',
            'created-at'     => 'Создано',
        ],

        'filters' => [
            'product'  => 'Товар',
            'location' => 'Место',
            'creator'  => 'Creator',
            'company'  => 'Компания',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Партия удалено',
                        'body'  => 'партия есть удалено successfully.',
                    ],

                    'error' => [
                        'title' => 'Партия could не быть удалено',
                        'body'  => 'партия нельзя быть удалено потому что это currently в использовать.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'print' => [
                'label' => 'Печать Штрихкод',
            ],

            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Партии удалено',
                        'body'  => 'партии есть удалено successfully.',
                    ],

                    'error' => [
                        'title' => 'Партии could не быть удалено',
                        'body'  => 'партии нельзя быть удалено потому что они currently в использовать.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Партия/Серийный номер Номер Подробности',

                'entries' => [
                    'name'        => 'Партия Название',
                    'product'     => 'Товар',
                    'reference'   => 'Ссылка',
                    'description' => 'Описание',
                    'on-hand-qty' => 'Вкл.-Вручную Количество',
                    'company'     => 'Компания',
                    'created-at'  => 'Создано',
                    'updated-at'  => 'Последний Обновлено',
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
