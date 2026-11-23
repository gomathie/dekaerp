<?php

return [
    'form' => [
        'fieldsets' => [
            'account-properties' => [
                'label' => 'Счёт Properties',

                'fields' => [
                    'income-account'                    => 'Доход Счёт',
                    'income-account-hint-tooltip'       => 'Этот счёт быть используется когда validating Счёт клиенту.',
                    'expense-account'                   => 'Расход Счёт',
                    'expense-account-hint-tooltip'      => 'расход recorded когда счёт поставщика validated, except менее Anglo-Saxon учёт с perpetual склад valuation, где это instead recognized как Себестоимость Sold когда Счёт клиенту validated.',
                    'down-payment-account'              => 'Вниз Оплата Счёт',
                    'down-payment-account-hint-tooltip' => 'Выбрать счёт до который вниз оплаты от этот категория быть recorded.',
                ],
            ],
        ],
    ],
];
