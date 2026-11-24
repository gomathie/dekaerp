<?php

return [
    'form' => [
        'sections' => [
            'fields' => [
                'payment-term'         => 'Условие оплаты',
                'company'              => 'Компания',
                'company-placeholder'  => 'Все компании',
                'early-discount'       => 'Скидка за досрочную оплату',
                'discount-days-prefix' => 'при оплате в течение',
                'discount-days-suffix' => 'дн.',
                'reduced-tax'          => 'Сниженный налог',
                'note'                 => 'Примечание',
                'status'               => 'Статус',
            ],
        ],

        'tabs' => [
            'due-terms' => [
                'title' => 'Сроки платежей',

                'repeater' => [
                    'due-terms' => [
                        'fields' => [
                            'value'                  => 'Значение',
                            'due'                    => 'Срок',
                            'delay-type'             => 'Тип отсрочки',
                            'days-on-the-next-month' => 'Число следующего месяца',
                            'days'                   => 'Дни',
                        ],
                    ],
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'payment-term' => 'Условие оплаты',
            'company'      => 'Компания',
            'created-at'   => 'Создано',
            'updated-at'   => 'Обновлено',
        ],

        'groups' => [
            'company-name'        => 'Название компании',
            'discount-days'       => 'Дни скидки',
            'early-pay-discount'  => 'Скидка за досрочную оплату',
            'payment-term'        => 'Условие оплаты',
            'display-on-invoice'  => 'Показывать в счёте',
            'early-discount'      => 'Скидка за досрочную оплату',
            'discount-percentage' => 'Скидка, %',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Условие оплаты восстановлено',
                    'body'  => 'Условие оплаты успешно восстановлено.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Условие оплаты удалено',
                    'body'  => 'Условие оплаты успешно удалено.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Условие оплаты удалено навсегда',
                        'body'  => 'Условие оплаты успешно удалено навсегда.',
                    ],

                    'error' => [
                        'title' => 'Не удалось удалить условие оплаты навсегда',
                        'body'  => 'Условие оплаты нельзя удалить навсегда: с ним связаны проводки.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Условия оплаты восстановлены',
                    'body'  => 'Условия оплаты успешно восстановлены.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Условия оплаты удалены',
                    'body'  => 'Условия оплаты успешно удалены.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Условия оплаты удалены навсегда',
                        'body'  => 'Условия оплаты успешно удалены навсегда.',
                    ],

                    'error' => [
                        'title' => 'Не удалось удалить условия оплаты навсегда',
                        'body'  => 'Условия оплаты нельзя удалить навсегда: с ними связаны проводки.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'entries' => [
                'payment-term'         => 'Условие оплаты',
                'early-discount'       => 'Скидка за досрочную оплату',
                'discount-percentage'  => 'Скидка, %',
                'discount-days-prefix' => 'при оплате в течение',
                'discount-days-suffix' => 'дн.',
                'reduced-tax'          => 'Сниженный налог',
                'note'                 => 'Примечание',
                'status'               => 'Статус',
            ],
        ],

        'tabs' => [
            'due-terms' => [
                'title' => 'Сроки платежей',

                'repeater' => [
                    'due-terms' => [
                        'entries' => [
                            'value'                  => 'Значение',
                            'due'                    => 'Срок',
                            'delay-type'             => 'Тип отсрочки',
                            'days-on-the-next-month' => 'Число следующего месяца',
                            'days'                   => 'Дни',
                        ],
                    ],
                ],
            ],
        ],
    ],
];
