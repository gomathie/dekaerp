<?php

return [
    'form' => [
        'value'                  => 'Значение',
        'due'                    => 'Срок',
        'delay-due'              => 'Отсрочка',
        'delay-type'             => 'Тип отсрочки',
        'days-on-the-next-month' => 'Число следующего месяца',
        'days'                   => 'Дни',
        'payment-term'           => 'Условие оплаты',
    ],

    'table' => [
        'columns' => [
            'due'          => 'Срок',
            'value'        => 'Значение',
            'value-amount' => 'Сумма значения',
            'after'        => 'Через',
            'delay-type'   => 'Тип отсрочки',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Срок платежа обновлён',
                    'body'  => 'Срок платежа успешно обновлён.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Срок платежа удалён',
                    'body'  => 'Срок платежа успешно удалён.',
                ],
            ],
        ],

        'header-actions' => [
            'create' => [
                'notification' => [
                    'title' => 'Срок платежа создан',
                    'body'  => 'Срок платежа успешно создан.',
                ],
            ],
        ],
    ],
];
