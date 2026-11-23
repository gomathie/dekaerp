<?php

return [
    'table' => [
        'columns' => [
            'reference'         => 'Ссылка',
            'total-amount'      => 'Итого Сумма',
            'confirmation-date' => 'Confirmation Дата',
            'status'            => 'Статус',
        ],
    ],

    'products' => [
        'columns' => [
            'product'    => 'Товар',
            'quantity'   => 'Количество',
            'unit-price' => 'Цена за единицу',
            'taxes'      => 'Налоги',
            'discount'   => 'Скидка %',
            'amount'     => 'Сумма',
        ],
    ],

    'infolist' => [
        'settings' => [
            'entries' => [
                'buyer' => 'Buyer',
            ],

            'actions' => [
                'accept' => [
                    'label' => 'Accept',

                    'notification' => [
                        'title' => 'Коммерческое предложение Accepted',
                        'body'  => 'Запрос котировок есть acknowledged successfully.',
                    ],

                    'message' => [
                        'body' => 'Запрос котировок есть acknowledged от поставщик.',
                    ],
                ],

                'decline' => [
                    'label' => 'Decline',

                    'notification' => [
                        'title' => 'Коммерческое предложение Declined',
                        'body'  => 'Запрос котировок есть declined successfully.',
                    ],

                    'message' => [
                        'body' => 'Запрос котировок есть declined от поставщик.',
                    ],
                ],

                'print' => [
                    'label' => 'Скачать/Печать',
                ],
            ],
        ],

        'general' => [
            'entries' => [
                'purchase-order'        => 'Заказ на закупку #:id',
                'quotation'             => 'Request для Коммерческое предложение #:id',
                'order-date'            => 'Заказ Дата',
                'from'                  => 'От',
                'confirmation-date'     => 'Confirmation Дата',
                'receipt-date'          => 'Приход Дата',
                'products'              => 'Товары',
                'untaxed-amount'        => 'Без налога Сумма',
                'tax-amount'            => 'Налог Сумма',
                'total'                 => 'Итого',
                'communication-history' => 'Сообщения История',
            ],
        ],
    ],
];
