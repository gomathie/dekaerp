<?php

return [
    'navigation' => [
        'title' => 'Bank Reconciliation',
    ],

    'import' => [
        'label'                => 'Import statement',
        'description'          => 'Upload the CSV file from your bank. Give the number of each column, counting from 1 on the left. Each bank account remembers its columns for next time; if the bank moves a column, it is found again by its heading.',
        'journal'              => 'Bank account',
        'file'                 => 'Statement file (CSV)',
        'name'                 => 'Statement name',
        'date-format'          => 'Date format',
        'has-header'           => 'First line is a header',
        'amount-help'          => 'Or leave empty and give the money in and money out columns.',
        'balance-help'         => 'Optional: the running balance. Its value on the last day is the closing balance.',
        'closing-balance'      => 'Closing balance on the statement',
        'closing-balance-help' => 'Optional. Without it or a balance column, the closing balance is the previous statement\'s plus these lines.',
        'done'                 => ':count lines imported.',

        'columns' => [
            'date'      => 'Date column',
            'label'     => 'Description column',
            'amount'    => 'Amount column',
            'money_in'  => 'Money in column',
            'money_out' => 'Money out column',
            'partner'   => 'Customer or supplier column',
            'reference' => 'Reference column',
            'balance'   => 'Balance column',
        ],
    ],

    'match-all' => [
        'label'       => 'Match all suggested (:count)',
        'heading'     => 'Match all suggested lines?',
        'description' => '{0} No line has exactly one suggested payment now.|{1} :count line has exactly one suggested payment of the same amount. It will be matched to it. Every other line stays as it is.|[2,*] :count lines have exactly one suggested payment of the same amount. Each will be matched to its payment. Every other line stays as it is.',
        'submit'      => 'Match',
        'done'        => '{0} No line was matched.|{1} :count line matched.|[2,*] :count lines matched.',
        'left'        => '{1} :count line was left open: its period is locked or it changed meanwhile.|[2,*] :count lines were left open: their period is locked or they changed meanwhile.',
    ],

    'balances' => [
        'heading'      => 'Balance check per account',
        'statement'    => 'Statement balance',
        'book'         => 'Book balance',
        'difference'   => 'Difference',
        'up-to'        => 'Statement up to :date',
        'no-statement' => 'No statement imported yet',
        'explain'      => 'Statement balance: the closing balance of the latest imported statement. Book balance: what the ledger holds on the account; payments count once matched to a statement line.',

        'state' => [
            'clean'      => 'Clean',
            'to-match'   => '{1} :count to match|[2,*] :count to match',
            'difference' => 'Difference to check',
            'none'       => 'No statement',
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
