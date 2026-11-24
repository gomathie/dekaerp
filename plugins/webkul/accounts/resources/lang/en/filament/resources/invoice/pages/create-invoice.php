<?php

return [
    'notification' => [
        'title' => 'Invoice created',
        'body'  => 'The invoice has been created successfully.',
    ],

    'direct-sale' => [
        'button'           => 'New direct sale',
        'walk-in-customer' => 'Walk-in Customer',
    ],

    'receipt' => [
        'label'     => 'Print receipt',
        'heading'   => 'Receipt',
        'title'     => 'Receipt',
        'number'    => 'Receipt no.',
        'date'      => 'Date',
        'customer'  => 'Customer',
        'served-by' => 'Served by',
        'item'      => 'Item',
        'qty'       => 'Qty',
        'amount'    => 'Amount',
        'subtotal'  => 'Subtotal',
        'tax'       => 'Tax',
        'total'     => 'Total',
        'paid'      => 'Paid',
        'balance'   => 'Balance due',
        'thank-you' => 'Thank you for your business.',
    ],

    'sale-currency' => [
        'heading'     => 'What currency was this sale in?',
        'description' => 'Choose the currency the customer pays in. The invoice is issued in it, and its value in your company currency is shown beside the totals.',
        'base'        => ':code (company currency)',
        'continue'    => 'Continue',
        'rate'        => 'Exchange rate',
        'feed'        => 'Today\'s rate from the daily feed, dated :date. Change it if you agreed another rate.',
        'stale'       => 'The latest rate on file is from :date, more than 3 days ago. Check it before you continue.',
        'no-rate'     => 'There is no rate on file for this currency. Type the rate you agreed.',
        'chatter'     => 'Exchange rate confirmed when the invoice was created: :rate.',
    ],
];
