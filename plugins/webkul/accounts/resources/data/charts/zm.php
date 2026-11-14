<?php

/**
 * Zambia: the standard IFRS-style chart with Zambian names for the tax and
 * payroll accounts, plus the statutory payables (accounting plan A4,
 * 2026-11-14). Account ids are kept; see ChartLocalizer.
 *
 * Tax: VAT 16 %. Not reviewed by a local accountant yet: check the rate
 * before calling a company's setup compliant.
 */
return [
    'name'      => 'Zambia',
    'countries' => ['ZM'],

    'accounts' => [
        '131000' => ['131000', 'VAT Input (recoverable)'],
        '251000' => ['251000', 'VAT Output (ZRA)'],
        '252000' => ['252000', 'VAT Payable to ZRA'],
        '230100' => ['230100', 'PAYE Payable (ZRA)'],
        '230200' => ['230200', 'NAPSA Payable'],
    ],

    'extra_accounts' => [
        ['230300', 'NHIMA Payable', 'liability_current', true],
        ['230400', 'Skills Development Levy Payable', 'liability_current', false],
        ['230500', 'Withholding Tax Payable (ZRA)', 'liability_current', false],
        ['132100', 'Withholding Tax Receivable', 'asset_current', false],
    ],

    'taxes' => [
        'ZM' => ['VAT 16 %', 16],
    ],

    'exempt_tax' => 'Exempt 0 %',
];
