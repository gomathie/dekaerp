<?php

return [
    'navigation' => [
        'title' => 'Поставщик Цена Списки',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Общее',

                'fields' => [
                    'vendor'                      => 'Поставщик',
                    'vendor-product-name'         => 'Поставщик Товар Название',
                    'vendor-product-name-tooltip' => 'vendor\'s товар название appear вкл. request для коммерческое предложение. Отпуск это blank до использовать внутренний товар название.',
                    'vendor-product-code'         => 'Поставщик Товар Код',
                    'vendor-product-code-tooltip' => 'vendor\'s товар код appear вкл. request для коммерческое предложение. Отпуск это blank до использовать внутренний код.',
                    'delay'                       => 'Доставка Lead Time (Дни)',
                    'delay-tooltip'               => 'lead time (в дни) от заказ на закупку confirmation до товар приход в склад. Используется от scheduler для автоматически закупка заказ planning.',
                ],
            ],

            'prices' => [
                'title'  => 'Цены',

                'fields' => [
                    'product'            => 'Товар',
                    'quantity'           => 'Количество',
                    'quantity-tooltip'   => 'minimum количество обязательно до закупка от этот поставщик до qualify для specified цена. Этот expressed в vendor\'s Товар Единица измерения или, если не задать, product\'s по умолчанию единица measure.',
                    'unit-price'         => 'Цена за единицу',
                    'unit-price-tooltip' => 'цена за единица для этот товар от поставщик, expressed в поставщик’s Товар Единица измерения или, если не задать, product\'s по умолчанию единица measure.',
                    'currency'           => 'Валюта',
                    'valid-from'         => 'Действительно От',
                    'valid-to'           => 'Действительно До',
                    'discount'           => 'Скидка (%)',
                    'company'            => 'Компания',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'vendor'              => 'Поставщик',
            'vendor-product-name' => 'Поставщик Товар Название',
            'vendor-product-code' => 'Поставщик Товар Код',
            'delay'               => 'Доставка Lead Time (Дни)',
            'product'             => 'Товар',
            'quantity'            => 'Количество',
            'unit-price'          => 'Цена за единицу',
            'currency'            => 'Валюта',
            'valid-from'          => 'Действительно От',
            'valid-to'            => 'Действительно До',
            'discount'            => 'Скидка (%)',
            'company'             => 'Компания',
            'created-at'          => 'Создано',
            'updated-at'          => 'Обновлено',
        ],

        'filters' => [
            'vendor'        => 'Фильтр от Поставщик',
            'product'       => 'Фильтр от Товар',
            'currency'      => 'Фильтр от Валюта',
            'company'       => 'Фильтр от Компания',
            'price-from'    => 'Minimum Цена',
            'price-to'      => 'Maximum Цена',
            'min-qty-from'  => 'Minimum Количество От',
            'min-qty-to'    => 'Minimum Количество До',
            'starts-from'   => 'Действительно От Дата',
            'ends-before'   => 'Действительно До Дата',
            'created-from'  => 'Создано От',
            'created-until' => 'Создано До',
        ],

        'groups' => [
            'vendor'     => 'Поставщик',
            'product'    => 'Товар',
            'created-at' => 'Создано',
            'updated-at' => 'Обновлено',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Поставщик Цена удалено',
                        'body'  => 'поставщик цена есть удалено successfully.',
                    ],

                    'error' => [
                        'title' => 'Поставщик Цена could не быть удалено',
                        'body'  => 'поставщик цена нельзя быть удалено потому что это currently в использовать.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Поставщик Цены удалено',
                        'body'  => 'поставщик цены есть удалено successfully.',
                    ],

                    'error' => [
                        'title' => 'Поставщик Цены could не быть удалено',
                        'body'  => 'поставщик цены нельзя быть удалено потому что они currently в использовать.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'  => 'Общее',

                'entries' => [
                    'vendor'                      => 'Поставщик',
                    'vendor-product-name'         => 'Поставщик Товар Название',
                    'vendor-product-name-tooltip' => 'vendor\'s товар название appear вкл. request для коммерческое предложение. Отпуск это blank до использовать внутренний товар название.',
                    'vendor-product-code'         => 'Поставщик Товар Код',
                    'vendor-product-code-tooltip' => 'vendor\'s товар код appear вкл. request для коммерческое предложение. Отпуск это blank до использовать внутренний код.',
                    'delay'                       => 'Доставка Lead Time (Дни)',
                    'delay-tooltip'               => 'lead time (в дни) от заказ на закупку confirmation до товар приход в склад. Используется от scheduler для автоматически закупка заказ planning.',
                ],
            ],

            'record-information' => [
                'title'  => 'Запись Сведения',

                'entries' => [
                    'created-by'   => 'Создано От',
                    'created-at'   => 'Создано',
                    'last-updated' => 'Последний Обновлено',
                ],
            ],

            'prices' => [
                'title'  => 'Цены',

                'entries' => [
                    'product'            => 'Товар',
                    'quantity'           => 'Количество',
                    'quantity-tooltip'   => 'minimum количество обязательно до закупка от этот поставщик до qualify для specified цена. Этот expressed в vendor\'s Товар Единица измерения или, если не задать, product\'s по умолчанию единица measure.',
                    'unit-price'         => 'Цена за единицу',
                    'unit-price-tooltip' => 'цена за единица для этот товар от поставщик, expressed в поставщик’s Товар Единица измерения или, если не задать, product\'s по умолчанию единица measure.',
                    'currency'           => 'Валюта',
                    'valid-from'         => 'Действительно От',
                    'valid-to'           => 'Действительно До',
                    'discount'           => 'Скидка (%)',
                    'company'            => 'Компания',
                ],
            ],
        ],
    ],
];
