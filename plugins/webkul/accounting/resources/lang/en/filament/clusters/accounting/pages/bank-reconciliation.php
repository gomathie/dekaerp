<?php

return [
    'navigation' => [
        'title' => 'Bank Reconciliation',
    ],

    'import' => [
        'label'       => 'Import statement',
        'description' => 'Upload the CSV file from your bank. Give the number of each column, counting from 1 on the left.',
        'journal'     => 'Bank account',
        'file'        => 'Statement file (CSV)',
        'name'        => 'Statement name',
        'date-format' => 'Date format',
        'has-header'  => 'First line is a header',
        'amount-help' => 'Or leave empty and give the money in and money out columns.',
        'done'        => ':count lines imported.',

        'columns' => [
            'date'      => 'Date column',
            'label'     => 'Description column',
            'amount'    => 'Amount column',
            'money_in'  => 'Money in column',
            'money_out' => 'Money out column',
            'partner'   => 'Customer or supplier column',
            'reference' => 'Reference column',
        ],
    ],

    'columns' => [
        'date'    => 'Date',
        'label'   => 'Description',
        'partner' => 'Customer or supplier',
        'journal' => 'Bank account',
        'amount'  => 'Amount',
        'status'  => 'Status',
        'payment' => 'Payment',
    ],

    'status' => [
        'reconciled'   => 'Reconciled',
        'to-reconcile' => 'To reconcile',
    ],

    'match' => [
        'label'       => 'Match payment',
        'description' => 'Payments in this bank account for the same amount that are still waiting for the bank.',
        'payment'     => 'Payment',
        'none'        => 'No payment matches. Register the payment on the invoice first, or book the line to an account.',
        'done'        => 'Line matched. The payment is now paid.',
    ],

    'book' => [
        'label'       => 'Book to account',
        'description' => 'For bank charges, interest or transfers: posts the line against the account you choose.',
        'account'     => 'Account',
        'entry-label' => 'Label',
        'done'        => 'Line booked.',
    ],

    'empty' => [
        'heading'     => 'Nothing to reconcile',
        'description' => 'Import a statement from your bank to start.',
    ],
];
