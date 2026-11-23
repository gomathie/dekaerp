<?php

return [
    'form' => [
        'fieldsets' => [
            'account-properties' => [
                'label' => 'Akaunti Properties',

                'fields' => [
                    'income-account'                    => 'Mapato Akaunti',
                    'income-account-hint-tooltip'       => 'Hii akaunti ita kuwa inatumika wakati validating Ankara ya mteja.',
                    'expense-account'                   => 'Gharama Akaunti',
                    'expense-account-hint-tooltip'      => 'gharama ni recorded wakati bili ya msambazaji ni validated, except chini ya Anglo-Saxon uhasibu na perpetual ghala valuation, wapi hii ni instead recognized kama Gharama ya bidhaa Sold wakati Ankara ya mteja ni validated.',
                    'down-payment-account'              => 'Chini Malipo Akaunti',
                    'down-payment-account-hint-tooltip' => 'Chagua akaunti hadi ambayo chini malipo kutoka hii kategoria ita kuwa recorded.',
                ],
            ],
        ],
    ],
];
