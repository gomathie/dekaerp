<?php

return [
    'title' => 'Record payment',

    'paid-now' => [
        'label'       => 'Paid now',
        'helper'      => 'Saving confirms the invoice and records the full payment.',
        'invalid'     => 'This way to pay cannot be used for this invoice.',
        'not-allowed' => 'You are not allowed to record payments.',
    ],

    'form' => [
        'more-options' => 'More options',
        'fields'       => [
            'paid-how'               => 'How was it paid?',
            'new-way'                => 'New way to pay (e.g. Wave, Card)',
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
