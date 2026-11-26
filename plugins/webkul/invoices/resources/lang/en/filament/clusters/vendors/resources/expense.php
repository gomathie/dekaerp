<?php

return [
    'title' => 'Expense',

    'navigation' => [
        'title' => 'Expenses',
    ],

    'form' => [
        'fields' => [
            'number'          => 'Number',
            'date'            => 'Date',
            'amount'          => 'Amount',
            'currency'        => 'Currency',
            'expense-account' => 'Expense account',
            'paid-from'       => 'Paid from',
            'vendor'          => 'Vendor',
            'vendor-name'     => 'Vendor name',
            'tax'             => 'Tax',
            'tax-amount'      => 'Tax amount',
            'reference'       => 'Reference',
            'note'            => 'Note',
            'receipt'         => 'Receipt',
            'entry'           => 'Journal entry',
            'reason'          => 'Reason for the change',
        ],

        'hints' => [
            'currency'        => 'Another currency uses the day\'s rate, as on invoices.',
            'expense-account' => 'This company\'s expense accounts only. The last one used is suggested first.',
            'paid-from'       => 'Every bank and cash account of the company.',
            'vendor'          => 'Optional. Type a new name to add the vendor.',
            'tax'             => 'Optional. Blank: no tax line.',
            'no-tax'          => 'No tax',
            'receipt'         => 'A photo or PDF, 10 MB at most. On a phone you can take a picture.',
            'reason'          => 'This expense is already in the books. Say why it changes.',
        ],

        'tax-included' => ':tax (included)',

        'summary' => [
            'empty' => 'Enter an amount to see what will be posted.',
            'posts' => 'On save, :total leaves the account paid from: :net to the expense account and :tax tax.',
        ],
    ],

    'table' => [
        'columns' => [
            'date'            => 'Date',
            'expense-account' => 'Expense account',
            'paid-from'       => 'Paid from',
            'vendor'          => 'Vendor',
            'reference'       => 'Reference',
            'amount'          => 'Amount',
            'receipt'         => 'Receipt',
        ],

        'total'  => 'Total',
        'export' => 'Export',

        'filters' => [
            'period'          => 'Period',
            'from'            => 'From',
            'until'           => 'Until',
            'paid-from'       => 'Paid from',
            'expense-account' => 'Expense account',
            'vendor'          => 'Vendor',
            'with-receipt'    => 'With receipt',
        ],
    ],

    'actions' => [
        'new'          => 'New expense',
        'save'         => 'Save',
        'save-and-new' => 'Save and new',

        'delete' => [
            'label'       => 'Delete',
            'description' => 'The expense\'s journal entry is cancelled, so the money comes back to the account it was paid from in the books.',
            'done'        => 'Expense deleted',
        ],
    ],

    'notifications' => [
        'created' => 'Expense saved and posted',
        'saved'   => 'Expense updated',
        'refused' => 'The expense was not saved',
        'failed'  => 'Something went wrong. Nothing was changed.',
    ],

    'export' => [
        'columns' => [
            'number'          => 'Number',
            'date'            => 'Date',
            'expense-account' => 'Expense account',
            'paid-from'       => 'Paid from',
            'vendor'          => 'Vendor',
            'reference'       => 'Reference',
            'currency'        => 'Currency',
            'amount'          => 'Amount',
            'tax'             => 'Tax',
            'amount-company'  => 'Amount in company currency',
            'note'            => 'Note',
        ],

        'notification' => [
            'completed' => 'Your expense export has completed and :count rows exported.',
            'failed'    => ':count rows failed to export.',
        ],
    ],
];
