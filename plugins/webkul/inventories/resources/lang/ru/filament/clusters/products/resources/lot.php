<?php

return [
    'navigation' => [
        'title' => 'Партии / серийные номера',
        'group' => 'Запасы',
    ],

    'global-search' => [
        'ref'     => 'Номер',
        'product' => 'Товар',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Общее',

                'fields' => [
                    'name'                   => 'Название',
                    'name-placeholder'       => 'напр. LOT/0001/20121',
                    'product'                => 'Товар',
                    'product-hint-tooltip'   => 'Товар, связанный с этой партией/серийным номером. Его нельзя изменить, если по нему уже были движения.',
                    'company'                => 'Компания',
                    'reference'              => 'Номер',
                    'reference-hint-tooltip' => 'Внутренний номер, если он отличается от номера партии/серийного номера производителя.',
                    'description'            => 'Описание',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'         => 'Название',
            'product'      => 'Товар',
            'on-hand-qty'  => 'Количество в наличии',
            'reference'    => 'Внутренний номер',
            'created-at'   => 'Создано',
            'updated-at'   => 'Обновлено',
        ],

        'groups' => [
            'product'        => 'Товар',
            'location'       => 'Местоположение',
            'created-at'     => 'Создано',
        ],

        'filters' => [
            'product'  => 'Товар',
            'location' => 'Местоположение',
            'creator'  => 'Автор',
            'company'  => 'Компания',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Партия удалена',
                        'body'  => 'Партия успешно удалена.',
                    ],

                    'error' => [
                        'title' => 'Не удалось удалить партию',
                        'body'  => 'Партию нельзя удалить, так как она используется.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'print' => [
                'label' => 'Печать штрихкода',
            ],

            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Партии удалены',
                        'body'  => 'Партии успешно удалены.',
                    ],

                    'error' => [
                        'title' => 'Не удалось удалить партии',
                        'body'  => 'Партии нельзя удалить, так как они используются.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Сведения о партии/серийном номере',

                'entries' => [
                    'name'        => 'Название партии',
                    'product'     => 'Товар',
                    'reference'   => 'Номер',
                    'description' => 'Описание',
                    'on-hand-qty' => 'Количество в наличии',
                    'company'     => 'Компания',
                    'created-at'  => 'Создано',
                    'updated-at'  => 'Последнее обновление',
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
