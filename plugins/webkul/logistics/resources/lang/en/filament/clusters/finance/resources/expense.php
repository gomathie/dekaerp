<?php

return [
    'navigation' => [
        'title' => 'Expenses',
    ],

    'form' => [
        'sections' => [
            'details'   => 'Expense details',
            'reference' => 'Reference and receipt',
        ],
        'fields' => [
            'company'          => 'Company',
            'date'             => 'Date',
            'amount'           => 'Amount',
            'currency'         => 'Currency',
            'category'         => 'Category',
            'paid-by'          => 'Paid by',
            'employee'         => 'Employee',
            'payee'            => 'Vendor or carrier',
            'shipment'         => 'Shipment',
            'trip'             => 'Trip',
            'vehicle'          => 'Vehicle',
            'vendor-reference' => 'Vendor reference',
            'description'      => 'Description',
            'receipt'          => 'Receipt',
        ],
    ],

    'table' => [
        'columns' => [
            'date'        => 'Date',
            'category'    => 'Category',
            'description' => 'Description',
            'amount'      => 'Amount',
            'state'       => 'Status',
            'shipment'    => 'Shipment',
            'company'     => 'Company',
        ],
        'filters' => [
            'state'    => 'Status',
            'category' => 'Category',
        ],
    ],

    'infolist' => [
        'sections' => [
            'summary' => 'Expense',
        ],
        'fields' => [
            'state'       => 'Status',
            'date'        => 'Date',
            'amount'      => 'Amount',
            'category'    => 'Category',
            'shipment'    => 'Shipment',
            'trip'        => 'Trip',
            'vehicle'     => 'Vehicle',
            'receipt'     => 'Receipt',
            'description' => 'Description',
        ],
    ],

    'actions' => [
        'submitExpense' => [
            'label'        => 'Submit',
            'heading'      => 'Submit this expense?',
            'notification' => 'Expense submitted.',
        ],
        'approveExpense' => [
            'label'        => 'Approve',
            'heading'      => 'Approve this expense?',
            'notification' => 'Expense approved.',
        ],
        'rejectExpense' => [
            'label'        => 'Reject',
            'heading'      => 'Reject this expense?',
            'notification' => 'Expense rejected.',
        ],
        'cannot-proceed' => 'This expense is not ready yet',
    ],
];
