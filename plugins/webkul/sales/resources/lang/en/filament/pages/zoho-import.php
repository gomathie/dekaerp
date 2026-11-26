<?php

return [
    'navigation' => 'Import from Zoho Books',
    'title'      => 'Import from Zoho Books',
    'intro'      => 'Bring a company\'s history from Zoho Books: choose the company, upload the export, check how its accounts, taxes and money accounts will be matched, then import. Documents are recorded oldest first with their Zoho numbers, and the page then compares Zoho with DEKA.',
    'empty'      => 'No import yet. Start with "Upload a Zoho export".',
    'unused'     => ':count Zoho accounts are not used by any document and are left out.',

    'propose' => [
        'label'       => 'Upload a Zoho export',
        'description' => 'The export file (JSON, up to 10 MB) is checked and matched to the company. Nothing is recorded yet.',
        'company'     => 'Company',
        'file'        => 'Zoho Books export (JSON)',
        'done'        => 'The matching is ready. Check it below, then import.',
    ],

    'propose-options' => [
        'numbering'      => 'Numbering of new documents',
        'numbering-deka' => 'DEKA\'s own yearly series (imported documents keep their Zoho numbers)',
        'numbering-zoho' => 'Continue Zoho\'s counter',
        'skip-samples'   => 'Skip Zoho demo contacts ("(Sample)") that no document uses',
    ],

    'compare' => [
        'label'       => 'Compare with a new Zoho export',
        'description' => 'For the parallel run: upload a fresh Zoho export to compare it with what DEKA holds now, including what was entered in DEKA directly. Nothing is written.',
        'queued'      => 'The comparison has started. This page follows it.',
    ],

    'download' => [
        'label' => 'Download the comparison (CSV)',
    ],

    'unmatched' => [
        'none' => 'Every invoice, payment and expense is in both systems.',
    ],

    'modes' => [
        'import'  => 'Import',
        'compare' => 'Comparison',
    ],

    'import' => [
        'label'       => 'Import',
        'confirm'     => 'Import into :company?',
        'description' => 'The whole history is recorded in this company. It cannot be undone from here; a stopped import continues from where it stopped when started again.',
        'queued'      => 'The import has started. This page follows it.',
    ],

    'run' => [
        'heading'    => 'Import into :company',
        'status'     => 'Status',
        'started-by' => 'Started by',
        'source'     => 'In the export',
        'working'    => 'Importing. This can take several minutes; the page refreshes by itself.',
    ],

    'statuses' => [
        'proposed' => 'Waiting for your approval',
        'queued'   => 'Queued',
        'running'  => 'Importing',
        'finished' => 'Finished',
        'failed'   => 'Stopped',
    ],

    'sections' => [
        'accounts'   => 'Accounts',
        'taxes'      => 'Taxes',
        'banks'      => 'Bank and cash accounts',
        'records'    => 'Products, contacts and numbering',
        'result'     => 'What was recorded',
        'unmatched'  => 'Documents in one system only',
        'comparison' => 'Zoho compared with DEKA',
    ],

    'columns' => [
        'zoho'       => 'Zoho',
        'uses'       => 'Uses',
        'action'     => 'Action',
        'deka'       => 'DEKA',
        'section'    => 'Section',
        'row'        => 'Row',
        'difference' => 'Difference',
    ],

    'actions' => [
        'map'    => 'use existing',
        'create' => 'create',
        'unused' => 'not used',
    ],

    'records' => [
        'items'          => 'Items: :create to create, :map matched to existing products.',
        'contacts'       => 'Contacts: :create to create, :map matched to existing contacts.',
        'numbering-deka' => 'New :kind use DEKA\'s own yearly series; imported ones keep their Zoho numbers.',
        'numbering'      => 'New :kind continue with :next.',
    ],

    'comparison' => [
        'all-ok'      => 'Every row matches.',
        'differences' => ':count rows differ.',
    ],

    'errors' => [
        'empty-file' => 'The uploaded file could not be read.',
        'no-run'     => 'Upload an export first.',
    ],
];
