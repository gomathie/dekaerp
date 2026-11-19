<?php

return [
    'title' => 'Pay',

    'form' => [
        'fields' => [
            'journal'                => 'Journal',
            'amount'                 => 'Amount',
            'currency'               => 'Currency',
            'payment-method-line'    => 'Payment Method Line',
            'payment-date'           => 'Payment Date',
            'recipient-bank-account' => 'Recipient Bank Account',
            'communication'          => 'Memo',
        ],
    ],

    'notifications' => [
        'payment-failed' => [
            'title'      => 'Payment Failed',
            'plain-body' => 'This payment could not be prepared. Our team has been notified. Please try again in a moment, or contact support.',
        ],
    ],
];
