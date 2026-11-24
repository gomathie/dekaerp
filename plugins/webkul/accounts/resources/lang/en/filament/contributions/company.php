<?php

return [
    'base-currency' => [
        'title'       => 'Base currency',
        'description' => 'Change the accounting currency of a company that already has entries. Super Admin only, and recorded in the activity log.',
        'action'      => 'Change base currency',
        'warning'     => 'This company has :count posted entries. They keep their own currency and rate, but reports from now on use the new base currency. The change, your name and your reason are recorded.',
        'changed'     => 'Base currency changed',
        'fields'      => [
            'currency' => 'New base currency',
            'reason'   => 'Reason',
        ],
    ],

    'sale-currency' => [
        'title'       => 'Currency question on new invoices',
        'description' => 'For companies that sell in more than one currency.',
        'field'       => 'Ask which currency each sale is in',
        'helper'      => 'A new invoice first asks for the currency and, for a foreign one, confirms the day\'s exchange rate. Only asked when the company has more than one currency.',
    ],
];
