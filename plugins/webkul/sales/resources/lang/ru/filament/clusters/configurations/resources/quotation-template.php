<?php

return [
    'title' => 'Коммерческое предложение Шаблон',

    'navigation' => [
        'title'  => 'Коммерческое предложение Шаблон',
        'group'  => 'Заказ на продажуs',
    ],

    'form' => [
        'tabs' => [
            'products' => [
                'title'  => 'Товары',
                'fields' => [
                    'products'     => 'Товары',
                    'name'         => 'Название',
                    'quantity'     => 'Количество',
                ],
            ],

            'terms-and-conditions' => [
                'title'  => 'Terms & Conditions',
                'fields' => [
                    'note-placeholder' => 'Запись ваш terms и conditions для коммерческие предложения.',
                ],
            ],
        ],

        'sections' => [
            'general' => [
                'title' => 'Общее Сведения',

                'fields' => [
                    'name'               => 'Название',
                    'quotation-validity' => 'Коммерческое предложение Validity',
                    'sale-journal'       => 'Продажа Журнал',
                ],
            ],

            'signature-and-payment' => [
                'title' => 'Signature & Оплаты',

                'fields' => [
                    'online-signature'      => 'Online Signature',
                    'online-payment'        => 'Online Оплата',
                    'prepayment-percentage' => 'Prepayment Процент',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'created-by'            => 'Создано от',
            'company'               => 'Компания',
            'name'                  => 'Название',
            'number-of-days'        => 'Номер дни',
            'journal'               => 'Продажа Журнал',
            'signature-required'    => 'Signature Обязательно',
            'payment-required'      => 'Оплата Обязательно',
            'prepayment-percentage' => 'Prepayment Процент',
        ],
        'groups'  => [
            'company' => 'Компания',
            'name'    => 'Название',
            'journal' => 'Журнал',
        ],
        'filters' => [
            'created-by' => 'Создано От',
            'company'    => 'Компания',
            'name'       => 'Название',
            'created-at' => 'Создано',
            'updated-at' => 'Обновлено',
        ],
        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Коммерческое предложение шаблон удалено',
                    'body'  => 'коммерческое предложение шаблон есть удалено successfully.',
                ],
            ],

        ],
        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Коммерческое предложение шаблон удалено',
                    'body'  => 'коммерческое предложение шаблон есть удалено successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'tabs' => [
            'products' => [
                'title' => 'Товары',
            ],
            'terms-and-conditions' => [
                'title' => 'Terms & Conditions',
            ],
        ],
        'sections' => [
            'general' => [
                'title' => 'Общее Сведения',
            ],
            'signature_and_payment' => [
                'title' => 'Signature & Оплата',
            ],
        ],
        'entries' => [
            'product'               => 'Товар',
            'description'           => 'Описание',
            'quantity'              => 'Количество',
            'unit-price'            => 'Цена за единицу',
            'section-name'          => 'Раздел Название',
            'note-title'            => 'Заметка Заголовок',
            'name'                  => 'Шаблон Название',
            'quotation-validity'    => 'Коммерческое предложение Validity',
            'sale-journal'          => 'Продажа Журнал',
            'online-signature'      => 'Online Signature',
            'online-payment'        => 'Online Оплата',
            'prepayment-percentage' => 'Prepayment Процент',
        ],
    ],
];
