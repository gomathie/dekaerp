<?php

return [
    'title' => 'Шаблон предложения',

    'navigation' => [
        'title'  => 'Шаблон предложения',
        'group'  => 'Заказы на продажу',
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
                'title'  => 'Условия',
                'fields' => [
                    'note-placeholder' => 'Укажите условия для коммерческих предложений.',
                ],
            ],
        ],

        'sections' => [
            'general' => [
                'title' => 'Общие сведения',

                'fields' => [
                    'name'               => 'Название',
                    'quotation-validity' => 'Срок действия предложения',
                    'sale-journal'       => 'Журнал продаж',
                ],
            ],

            'signature-and-payment' => [
                'title' => 'Подпись и оплата',

                'fields' => [
                    'online-signature'      => 'Онлайн-подпись',
                    'online-payment'        => 'Онлайн-оплата',
                    'prepayment-percentage' => 'Процент предоплаты',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'created-by'            => 'Кем создано',
            'company'               => 'Компания',
            'name'                  => 'Название',
            'number-of-days'        => 'Количество дней',
            'journal'               => 'Журнал продаж',
            'signature-required'    => 'Нужна подпись',
            'payment-required'      => 'Нужна оплата',
            'prepayment-percentage' => 'Процент предоплаты',
        ],
        'groups'  => [
            'company' => 'Компания',
            'name'    => 'Название',
            'journal' => 'Журнал',
        ],
        'filters' => [
            'created-by' => 'Кем создано',
            'company'    => 'Компания',
            'name'       => 'Название',
            'created-at' => 'Создано',
            'updated-at' => 'Обновлено',
        ],
        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Шаблон предложения удалён',
                    'body'  => 'Шаблон предложения успешно удалён.',
                ],
            ],

        ],
        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Шаблон предложения удалён',
                    'body'  => 'Шаблон предложения успешно удалён.',
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
                'title' => 'Условия',
            ],
        ],
        'sections' => [
            'general' => [
                'title' => 'Общие сведения',
            ],
            'signature_and_payment' => [
                'title' => 'Подпись и оплата',
            ],
        ],
        'entries' => [
            'product'               => 'Товар',
            'description'           => 'Описание',
            'quantity'              => 'Количество',
            'unit-price'            => 'Цена за ед.',
            'section-name'          => 'Название раздела',
            'note-title'            => 'Заголовок примечания',
            'name'                  => 'Название шаблона',
            'quotation-validity'    => 'Срок действия предложения',
            'sale-journal'          => 'Журнал продаж',
            'online-signature'      => 'Онлайн-подпись',
            'online-payment'        => 'Онлайн-оплата',
            'prepayment-percentage' => 'Процент предоплаты',
        ],
    ],
];
