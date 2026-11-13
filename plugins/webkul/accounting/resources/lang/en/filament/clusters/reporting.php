<?php

return [
    'navigation' => [
        'title' => 'Reporting',
    ],
    'common' => [
        'from-to'                  => ':report - From :from to :to',
        'expand-all'               => 'Expand All',
        'collapse-all'             => 'Collapse All',
        'account'                  => 'Account',
        'date'                     => 'Date',
        'communication'            => 'Communication',
        'partner'                  => 'Partner',
        'journal'                  => 'Journal',
        'invoice-date'             => 'Invoice Date',
        'due-date'                 => 'Due Date',
        'debit'                    => 'Debit',
        'credit'                   => 'Credit',
        'balance'                  => 'Balance',
        'total'                    => 'Total',
        'opening-balance'          => 'Opening Balance',
        'initial-balance'          => 'Initial Balance',
        'end-balance'              => 'End Balance',
        'not-due'                  => 'Not due',
        'no-data'                  => 'No data available',
        'no-accounts-transactions' => 'No accounts with transactions in this period',
    ],
    'pages' => [
        'balance-sheet' => [
            'navigation' => [
                'title' => 'Balance Sheet',
                'group' => 'Statement Reports',
            ],
            'actions' => [
                'export-excel' => 'Export to Excel',
                'export-pdf'   => 'Export to PDF',
            ],
            'filters' => [
                'date-range' => 'Date Range',
                'journals'   => 'Journals',
            ],
            'content' => [
                'sections' => [
                    'assets' => [
                        'title'       => 'ASSETS',
                        'total-label' => 'Total ASSETS',
                        'subsections' => [
                            'current-assets' => [
                                'title'       => 'Current Assets',
                                'total-label' => 'Total Current Assets',
                            ],
                            'fixed-assets' => [
                                'title'       => 'Fixed Assets',
                                'total-label' => 'Total Fixed Assets',
                            ],
                            'non-current-assets' => [
                                'title'       => 'Non-current Assets',
                                'total-label' => 'Total Non-current Assets',
                            ],
                        ],
                    ],
                    'liabilities' => [
                        'title'       => 'LIABILITIES',
                        'total-label' => 'Total LIABILITIES',
                        'subsections' => [
                            'current-liabilities' => [
                                'title'       => 'Current Liabilities',
                                'total-label' => 'Total Current Liabilities',
                            ],
                            'non-current-liabilities' => [
                                'title'       => 'Non-current Liabilities',
                                'total-label' => 'Total Non-current Liabilities',
                            ],
                        ],
                    ],
                    'equity' => [
                        'title'       => 'EQUITY',
                        'total-label' => 'Total EQUITY',
                        'subsections' => [
                            'unallocated-earnings' => [
                                'title'          => 'Unallocated Earnings',
                                'current-year'   => 'Current Year Unallocated Earnings',
                                'previous-years' => 'Previous Years Unallocated Earnings',
                                'total-label'    => 'Total Unallocated Earnings',
                            ],
                            'retained-earnings' => [
                                'title'       => 'Retained Earnings',
                                'total-label' => 'Total Retained Earnings',
                            ],
                        ],
                    ],
                ],
                'grand-total-label' => 'LIABILITIES + EQUITY',
            ],
        ],
        'profit-loss' => [
            'navigation' => [
                'title' => 'Profit & Loss',
                'group' => 'Statement Reports',
            ],
            'actions' => [
                'export-excel' => 'Export to Excel',
                'export-pdf'   => 'Export to PDF',
            ],
            'filters' => [
                'date-range' => 'Date Range',
                'journals'   => 'Journals',
            ],
            'content' => [
                'sections' => [
                    'revenue' => [
                        'title'         => 'REVENUE',
                        'total-label'   => 'Total Revenue',
                        'empty-message' => 'No revenue accounts with transactions in this period',
                    ],
                    'expenses' => [
                        'title'         => 'EXPENSES',
                        'total-label'   => 'Total Expenses',
                        'empty-message' => 'No expense accounts with transactions in this period',
                    ],
                ],
            ],
        ],
        'general-ledger' => [
            'navigation' => [
                'title' => 'General Ledger',
                'group' => 'Audit Reports',
            ],
            'actions' => [
                'export-excel' => 'Export to Excel',
                'export-pdf'   => 'Export to PDF',
            ],
            'filters' => [
                'date-range' => 'Date Range',
                'journals'   => 'Journals',
            ],
        ],
        'tax-summary' => [
            'navigation' => [
                'title' => 'Tax Summary',
                'group' => 'Audit Reports',
            ],
            'actions'  => ['export-excel' => 'Export to Excel'],
            'filters'  => ['date-range' => 'Date Range'],
            'columns'  => ['tax' => 'Tax', 'rate' => 'Rate', 'base' => 'Taxable amount', 'amount' => 'Tax'],
            'sections' => ['sales' => 'On sales (collected)', 'purchases' => 'On purchases (paid)'],
            'totals'   => [
                'collected'       => 'Total collected',
                'paid'            => 'Total paid',
                'net'             => 'Net',
                'net-payable'     => 'Net tax to pay',
                'net-reclaimable' => 'Net tax to reclaim',
            ],
            'empty' => 'No tax in this period.',
            'note'  => 'From posted invoices, bills, credit notes and refunds dated in the period. Credit notes and refunds reduce the totals. Check with your accountant before filing.',
        ],
        'cash-flow' => [
            'navigation' => [
                'title' => 'Cash Flow',
                'group' => 'Statement Reports',
            ],
            'actions'  => ['export-excel' => 'Export to Excel'],
            'filters'  => ['date-range' => 'Date Range'],
            'columns'  => ['account' => 'From or to', 'in' => 'Money in', 'out' => 'Money out', 'net' => 'Net'],
            'sections' => ['operating' => 'Operating activities', 'investing' => 'Investing activities', 'financing' => 'Financing activities'],
            'totals'   => [
                'opening' => 'Cash at start of period',
                'section' => 'Net cash',
                'net'     => 'Net change in cash',
                'closing' => 'Cash at end of period',
            ],
            'other' => 'Other',
            'empty' => 'No cash movement in this period.',
            'note'  => 'From posted entries on bank and cash accounts. Each entry is shown under the account on its other side. Transfers between your own bank and cash accounts are left out.',
        ],
        'trial-balance' => [
            'navigation' => [
                'title' => 'Trial Balance',
                'group' => 'Audit Reports',
            ],
            'actions' => [
                'export-excel' => 'Export to Excel',
                'export-pdf'   => 'Export to PDF',
            ],
            'filters' => [
                'date-range' => 'Date Range',
                'journals'   => 'Journals',
            ],
        ],
        'partner-ledger' => [
            'navigation' => [
                'title' => 'Partner Ledger',
                'group' => 'Partner Reports',
            ],
            'actions' => [
                'export-excel' => 'Export Excel',
                'export-pdf'   => 'Export PDF',
            ],
            'filters' => [
                'date-range' => 'Date Range',
                'partners'   => 'Partners',
                'journals'   => 'Journals',
            ],
        ],
        'aged-receivable' => [
            'navigation' => [
                'title' => 'Aged Receivable',
                'group' => 'Partner Reports',
            ],
            'actions' => [
                'export-excel' => 'Export Excel',
                'export-pdf'   => 'Export PDF',
            ],
            'filters' => [
                'as-of'         => 'As of',
                'based-on'      => 'Based on',
                'period-length' => 'Period Length (days)',
                'journals'      => 'Journals',
                'partners'      => 'Partners',
                'entries'       => 'Entries',
                'options'       => [
                    'due-date'       => 'Due Date',
                    'invoice-date'   => 'Invoice Date',
                    'days-30'        => '30 Days',
                    'days-60'        => '60 Days',
                    'days-90'        => '90 Days',
                    'posted-entries' => 'Posted Entries',
                    'all-entries'    => 'All Entries',
                ],
            ],
        ],
        'aged-payable' => [
            'navigation' => [
                'title' => 'Aged Payable',
                'group' => 'Partner Reports',
            ],
            'actions' => [
                'export-excel' => 'Export Excel',
                'export-pdf'   => 'Export PDF',
            ],
            'filters' => [
                'as-of'         => 'As of',
                'based-on'      => 'Based on',
                'period-length' => 'Period Length (days)',
                'journals'      => 'Journals',
                'partners'      => 'Partners',
                'entries'       => 'Entries',
                'options'       => [
                    'due-date'       => 'Due Date',
                    'invoice-date'   => 'Invoice Date',
                    'days-30'        => '30 Days',
                    'days-60'        => '60 Days',
                    'days-90'        => '90 Days',
                    'posted-entries' => 'Posted Entries',
                    'all-entries'    => 'All Entries',
                ],
            ],
        ],
    ],
];
