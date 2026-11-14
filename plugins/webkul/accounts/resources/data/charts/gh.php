<?php

/**
 * Ghana: the standard IFRS-style chart with Ghanaian names for the tax and
 * payroll accounts, plus the statutory payables (accounting plan A4,
 * 2026-11-14). Account ids are kept; see ChartLocalizer.
 *
 * Tax: VAT 15 % with NHIL 2.5 % and GETFund 2.5 %, 20 % in total, as one
 * rate. A starting value: the company's users adjust it, or split the
 * levies into separate taxes, under Accounting → Configuration.
 */
return [
    'name'      => 'Ghana',
    'countries' => ['GH'],

    'accounts' => [
        '131000' => ['131000', 'VAT Input (recoverable)'],
        '251000' => ['251000', 'VAT Output (GRA)'],
        '252000' => ['252000', 'VAT Payable to GRA'],
        '230100' => ['230100', 'PAYE Payable (GRA)'],
        '230200' => ['230200', 'SSNIT Payable (Tier 1)'],
    ],

    'extra_accounts' => [
        ['230300', 'Tier 2 Pension Payable', 'liability_current', true],
        ['230400', 'Withholding Tax Payable (GRA)', 'liability_current', false],
        ['132100', 'Withholding Tax Receivable', 'asset_current', false],
    ],

    'taxes' => [
        'GH' => ['VAT 20 % (VAT 15 % + NHIL 2.5 % + GETFund 2.5 %)', 20],
    ],

    'exempt_tax' => 'Exempt 0 %',
];
