<?php

return [
    'navigation' => [
        'title' => 'Закупка Agreements',
        'group' => 'Закупка',
    ],

    'global-search' => [
        'vendor' => 'Поставщик',
        'type'   => 'Тип',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Общее',

                'fields' => [
                    'vendor'                => 'Поставщик',
                    'valid-from'            => 'Действительно От',
                    'valid-to'              => 'Действительно До',
                    'buyer'                 => 'Buyer',
                    'reference'             => 'Ссылка',
                    'reference-placeholder' => 'eg. PO/123',
                    'agreement-type'        => 'Agreement Тип',
                    'company'               => 'Компания',
                    'currency'              => 'Валюта',
                ],
            ],
        ],

        'tabs' => [
            'products' => [
                'title' => 'Товары',

                'columns' => [
                    'product'    => 'Товар',
                    'quantity'   => 'Количество',
                    'ordered'    => 'Ordered',
                    'uom'        => 'Единица измерения',
                    'unit-price' => 'Цена за единицу',
                ],

                'fields' => [
                    'product'    => 'Товар',
                    'quantity'   => 'Количество',
                    'ordered'    => 'Ordered',
                    'uom'        => 'Единица измерения',
                    'unit-price' => 'Цена за единицу',
                ],
            ],

            'additional' => [
                'title' => 'Additional Сведения',
            ],

            'terms' => [
                'title' => 'Terms и Conditions',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'agreement'      => 'Agreement',
            'vendor'         => 'Поставщик',
            'agreement-type' => 'Agreement Тип',
            'buyer'          => 'Buyer',
            'company'        => 'Компания',
            'valid-from'     => 'Действительно От',
            'valid-to'       => 'Действительно До',
            'reference'      => 'Ссылка',
            'status'         => 'Статус',
        ],

        'groups' => [
            'agreement-type' => 'Agreement Тип',
            'vendor'         => 'Поставщик',
            'state'          => 'Состояние',
            'created-at'     => 'Создано',
            'updated-at'     => 'Обновлено',
        ],

        'filters' => [
            'agreement'      => 'Agreement',
            'vendor'         => 'Поставщик',
            'agreement-type' => 'Agreement Тип',
            'buyer'          => 'Buyer',
            'company'        => 'Компания',
            'valid-from'     => 'Действительно От',
            'valid-to'       => 'Действительно До',
            'reference'      => 'Ссылка',
            'status'         => 'Статус',
            'created-at'     => 'Создано',
            'updated-at'     => 'Обновлено',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Закупка Agreement удалено',
                    'body'  => 'закупка agreement есть удалено successfully.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Закупка Agreement restored',
                    'body'  => 'закупка agreement есть restored successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Закупка Agreement permanently удалено',
                        'body'  => 'закупка agreement есть permanently удалено successfully.',
                    ],

                    'error' => [
                        'title' => 'Закупка Agreement could не быть удалено',
                        'body'  => 'закупка agreement нельзя быть удалено потому что это currently в использовать.',
                    ],

                    'warning' => [
                        'title' => 'Закупка Agreement нельзя быть удалено',
                        'body'  => 'Только закупка agreements в Черновик или Отменён состояние можно быть удалено.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Закупка Agreements удалено',
                    'body'  => 'закупка agreements есть удалено successfully.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Закупка Agreements restored',
                    'body'  => 'закупка agreements есть restored successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Закупка Agreements permanently удалено',
                        'body'  => 'закупка agreements есть permanently удалено successfully.',
                    ],

                    'error' => [
                        'title' => 'Закупка Agreements could не быть удалено',
                        'body'  => 'закупка agreements нельзя быть удалено потому что они currently в использовать.',
                    ],

                    'warning' => [
                        'title' => 'Закупка Agreement нельзя быть удалено',
                        'body'  => 'Только закупка agreements в Черновик или Отменён состояние можно быть удалено.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Общее',

                'entries' => [
                    'vendor'                => 'Поставщик',
                    'valid-from'            => 'Действительно От',
                    'valid-to'              => 'Действительно До',
                    'buyer'                 => 'Buyer',
                    'reference'             => 'Ссылка',
                    'reference-placeholder' => 'eg. PO/123',
                    'agreement-type'        => 'Agreement Тип',
                    'company'               => 'Компания',
                    'currency'              => 'Валюта',
                ],
            ],

            'metadata' => [
                'title' => 'Metadata',

                'entries' => [
                    'created-at' => 'Создано',
                    'created-by' => 'Создано От',
                    'updated-at' => 'Обновлено',
                ],
            ],
        ],

        'tabs' => [
            'products' => [
                'title' => 'Товары',

                'entries' => [
                    'product'    => 'Товар',
                    'quantity'   => 'Количество',
                    'ordered'    => 'Ordered',
                    'uom'        => 'Единица измерения',
                    'unit-price' => 'Цена за единицу',
                ],
            ],

            'additional' => [
                'title' => 'Additional Сведения',
            ],

            'terms' => [
                'title' => 'Terms и Conditions',
            ],
        ],
    ],
];
