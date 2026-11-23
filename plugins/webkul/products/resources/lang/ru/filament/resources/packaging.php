<?php

return [
    'form' => [
        'name'    => 'Название',
        'barcode' => 'Штрихкод',
        'product' => 'Товар',
        'routes'  => 'Маршруты',
        'qty'     => 'Qty',
        'company' => 'Компания',
    ],

    'table' => [
        'columns' => [
            'name'       => 'Название',
            'product'    => 'Товар',
            'routes'     => 'Маршруты',
            'qty'        => 'Qty',
            'company'    => 'Компания',
            'barcode'    => 'Штрихкод',
            'created-at' => 'Создано',
            'updated-at' => 'Обновлено',
        ],

        'groups' => [
            'product'    => 'Товар',
            'created-at' => 'Создано',
            'updated-at' => 'Обновлено',
        ],

        'filters' => [
            'product' => 'Товар',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Упаковка обновить',
                    'body'  => 'упаковка есть обновить successfully.',
                ],
            ],

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
            'print' => [
                'label' => 'Печать',
            ],

            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Packagings удалено',
                        'body'  => 'packagings есть удалено successfully.',
                    ],

                    'error' => [
                        'title' => 'Packagings could не быть удалено',
                        'body'  => 'packagings нельзя быть удалено потому что они currently в использовать.',
                    ],
                ],
            ],
        ],

        'empty-state-actions' => [
            'create' => [
                'label' => 'Новое Упаковка',

                'notification' => [
                    'title' => 'Упаковка создано',
                    'body'  => 'упаковка есть создано successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Общее Сведения',

                'entries' => [
                    'name'    => 'Упаковка Название',
                    'barcode' => 'Штрихкод',
                    'product' => 'Товар',
                    'qty'     => 'Количество',
                ],
            ],

            'organization' => [
                'title' => 'Organization Подробности',

                'entries' => [
                    'company'    => 'Компания',
                    'creator'    => 'Создано От',
                    'created_at' => 'Создано',
                    'updated_at' => 'Последний Обновлено',
                ],
            ],
        ],
    ],
];
