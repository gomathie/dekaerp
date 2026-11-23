<?php

return [
    'form' => [
        'sections' => [
            'fields' => [
                'payment-term'         => 'Условие оплаты',
                'company'              => 'Компания',
                'company-placeholder'  => 'Все Компании',
                'early-discount'       => 'Early Скидка',
                'discount-days-prefix' => 'если оплачен в пределах',
                'discount-days-suffix' => 'дни',
                'reduced-tax'          => 'Reduced налог',
                'note'                 => 'Заметка',
                'status'               => 'Статус',
            ],
        ],

        'tabs' => [
            'due-terms' => [
                'title' => 'Due Terms',

                'repeater' => [
                    'due-terms' => [
                        'fields' => [
                            'value'                  => 'Значение',
                            'due'                    => 'Due',
                            'delay-type'             => 'Delay Тип',
                            'days-on-the-next-month' => 'Дни вкл. далее месяц',
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
            'company-name'        => 'Компания Название',
            'discount-days'       => 'Скидка Дни',
            'early-pay-discount'  => 'Early Pay Скидка',
            'payment-term'        => 'Условие оплаты',
            'display-on-invoice'  => 'Display вкл. Счёт',
            'early-discount'      => 'Early Скидка',
            'discount-percentage' => 'Скидка',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Условие оплаты restored',
                    'body'  => 'Условие оплаты есть restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Условие оплаты удалено',
                    'body'  => 'Условие оплаты есть удалено successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Условие оплаты force удалено',
                        'body'  => 'Условие оплаты есть force удалено successfully.',
                    ],

                    'error' => [
                        'title' => 'Условие оплаты force deletion не удалось',
                        'body'  => 'Условие оплаты could не быть force удалено потому что это associated проводки.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Условия оплаты restored',
                    'body'  => 'условия оплаты есть restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Условия оплаты удалено',
                    'body'  => 'условия оплаты есть удалено successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Условия оплаты force удалено',
                        'body'  => 'условия оплаты есть force удалено successfully.',
                    ],

                    'error' => [
                        'title' => 'Условия оплаты force deletion не удалось',
                        'body'  => 'условия оплаты could не быть force удалено потому что они есть associated проводки.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'entries' => [
                'payment-term'         => 'Условие оплаты',
                'early-discount'       => 'Early Скидка',
                'discount-percentage'  => 'Скидка',
                'discount-days-prefix' => 'если оплачен в пределах',
                'discount-days-suffix' => 'дни',
                'reduced-tax'          => 'Reduced налог',
                'note'                 => 'Заметка',
                'status'               => 'Статус',
            ],
        ],

        'tabs' => [
            'due-terms' => [
                'title' => 'Due Terms',

                'repeater' => [
                    'due-terms' => [
                        'entries' => [
                            'value'                  => 'Значение',
                            'due'                    => 'Due',
                            'delay-type'             => 'Delay Тип',
                            'days-on-the-next-month' => 'Дни вкл. далее месяц',
                            'days'                   => 'Дни',
                        ],
                    ],
                ],
            ],
        ],
    ],
];
