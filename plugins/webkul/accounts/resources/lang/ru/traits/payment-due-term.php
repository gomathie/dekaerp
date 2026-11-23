<?php

return [
    'form' => [
        'value'                  => 'Значение',
        'due'                    => 'Due',
        'delay-due'              => 'Delay Due',
        'delay-type'             => 'Delay Тип',
        'days-on-the-next-month' => 'Дни вкл. далее месяц',
        'days'                   => 'Дни',
        'payment-term'           => 'Условие оплаты',
    ],

    'table' => [
        'columns' => [
            'due'          => 'Due',
            'value'        => 'Значение',
            'value-amount' => 'Значение Сумма',
            'after'        => 'После',
            'delay-type'   => 'Delay Тип',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Оплата Due Term обновлено',
                    'body'  => 'оплата due term есть обновлено successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Оплата Due Term удалено',
                    'body'  => 'оплата due term есть удалено successfully.',
                ],
            ],
        ],

        'header-actions' => [
            'create' => [
                'notification' => [
                    'title' => 'Оплата Due Term создано',
                    'body'  => 'оплата due term есть создано successfully.',
                ],
            ],
        ],
    ],
];
