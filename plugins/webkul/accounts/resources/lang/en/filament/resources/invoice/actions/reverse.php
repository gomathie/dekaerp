<?php

return [
    'title' => 'Reverse',

    'modal' => [
        'heading' => 'Create Reverse',

        'form' => [
            'reason'  => 'Reason displayed on Reverse',
            'journal' => 'Journal',
            'date'    => 'Date',
        ],
    ],

    'one-step' => [
        'scope'               => 'What to credit',
        'whole'               => 'The whole invoice',
        'lines'               => 'Some lines',
        'line-list'           => 'Lines',
        'disposition'         => 'Then',
        'apply'               => 'Take it off the invoice',
        'refund'              => 'Pay it back',
        'credit'              => 'Keep as credit',
        'saved'               => 'Credit note posted.',
        'history'             => 'Credit note posted (:disposition). Reason: :reason',
        'reason'              => 'A reason is required.',
        'invoice'             => 'Only a posted customer invoice can be credited this way.',
        'lines-invalid'       => 'Choose lines from this invoice.',
        'disposition-invalid' => 'Choose what to do with the credit note.',
        'not-allowed'         => 'You are not allowed to pay this back.',
        'company'             => 'You cannot credit an invoice in that company.',
        'journal'             => 'Choose a bank, cash or card journal for this company.',
        'not-settled'         => 'The money was not paid back.',
        'not-applied'         => 'The credit note was not taken off the invoice.',
    ],
];
