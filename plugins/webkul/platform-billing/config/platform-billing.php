<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Platform billing currency
    |--------------------------------------------------------------------------
    |
    | D-SAAS-01: launch plan prices and platform invoices are in USD only,
    | expressed as explicit currency-specific amounts so another currency can be
    | added later without implicit FX conversion. A money value carries its own
    | currency, and adding two different currencies is refused rather than
    | converted.
    |
    */

    'currency' => 'USD',

    /*
    |--------------------------------------------------------------------------
    | Document numbering
    |--------------------------------------------------------------------------
    |
    | D-SAAS-05: `DEKA-INV-{compact year}-{six-digit sequence}`, and the same
    | shape with `DEKA-CN` for credit notes. The compact year drops the third
    | digit of a four-digit year (2026 -> 226), which keeps the number short
    | without ever colliding across years.
    |
    */

    'document_prefixes' => [
        'invoice'     => 'DEKA-INV',
        'credit_note' => 'DEKA-CN',
    ],

    /*
    |--------------------------------------------------------------------------
    | Issuer identity (printed on documents)
    |--------------------------------------------------------------------------
    |
    | Defaults, editable by Super Admin (D-SAAS-05). Every posted document
    | snapshots these values, so changing them later never rewrites history.
    |
    */

    'issuer' => [
        'name'     => 'DEKA',
        'tax_rate' => '15.0000',
    ],
];
