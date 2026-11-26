<?php

return [
    'errors' => [
        'date'            => 'Enter the date the expense was paid.',
        'amount'          => 'Enter an amount greater than zero.',
        'journal'         => 'Choose a bank or cash account of this company.',
        'account'         => 'Choose an expense account of this company.',
        'tax'             => 'Choose a purchase tax of this company.',
        'vendor'          => 'Choose a vendor of this company.',
        'vendor-name'     => 'Enter the vendor\'s name (255 characters at most).',
        'currency'        => 'Choose a currency this company may use.',
        'no-rate'         => 'There is no exchange rate for :currency on this date. Add one under Accounting configuration, or record the expense in the company currency.',
        'receipt'         => 'The receipt file could not be accepted. Upload it again.',
        'reference'       => 'The reference may have 255 characters at most.',
        'reason-required' => 'Give a reason: this expense is already in the books.',
    ],
];
