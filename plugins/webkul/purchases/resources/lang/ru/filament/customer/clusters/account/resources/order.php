<?php

return [
    'table' => [
        'columns' => [
            'reference'         => 'Номер',
            'total-amount'      => 'Общая сумма',
            'confirmation-date' => 'Дата подтверждения',
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
                'buyer' => 'Закупщик',
            ],

            'actions' => [
                'accept' => [
                    'label' => 'Принять',

                    'notification' => [
                        'title' => 'Предложение принято',
                        'body'  => 'Запрос цены успешно принят.',
                    ],

                    'message' => [
                        'body' => 'Поставщик принял запрос цены.',
                    ],
                ],

                'decline' => [
                    'label' => 'Отклонить',

                    'notification' => [
                        'title' => 'Предложение отклонено',
                        'body'  => 'Запрос цены успешно отклонён.',
                    ],

                    'message' => [
                        'body' => 'Поставщик отклонил запрос цены.',
                    ],
                ],

                'print' => [
                    'label' => 'Скачать/печать',
                ],
            ],
        ],

        'general' => [
            'entries' => [
                'purchase-order'        => 'Заказ на закупку № :id',
                'quotation'             => 'Запрос цены № :id',
                'order-date'            => 'Дата заказа',
                'from'                  => 'С',
                'confirmation-date'     => 'Дата подтверждения',
                'receipt-date'          => 'Дата поступления',
                'products'              => 'Товары',
                'untaxed-amount'        => 'Сумма без налогов',
                'tax-amount'            => 'Сумма налога',
                'total'                 => 'Итого',
                'communication-history' => 'История переписки',
            ],
        ],
    ],
];
