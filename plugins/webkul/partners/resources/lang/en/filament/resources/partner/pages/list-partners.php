<?php

return [
    'title' => 'Contacts',

    'header-actions' => [
        'create' => [
            'label' => 'Create Contact',
        ],

        'import' => [
            'label' => 'Import from Excel',

            'modal' => [
                'heading'                  => 'Import contacts from a spreadsheet',
                'description'              => 'Download the example file, fill in one contact per row, then upload it here. Up to :max contacts per upload.',
                'submit'                   => 'Import contacts',
                'download-template-csv'    => 'Download example (CSV)',
                'download-template-excel'  => 'Download example (Excel)',

                'file' => [
                    'label'  => 'Completed file',
                    'helper' => 'Excel (.xlsx, .xls) or CSV. Keep the heading row as it is. Required column: :columns. Rows with a problem are reported by row number and the rest are still imported.',
                ],
            ],

            'notifications' => [
                'imported'        => ':count contacts imported',
                'skipped'         => ':count rows were skipped:',
                'nothing-imported' => 'No contacts were imported',
                'more-errors'     => '... and :count more rows.',
                'unreadable'      => 'That file could not be read. Please upload an Excel or CSV file.',
            ],
        ],
    ],

    'tabs' => [
        'individuals' => 'Individuals',
        'companies'   => 'Companies',
        'employees'   => 'Employees',
        'customers'   => 'Customers',
        'vendors'     => 'Vendors',
        'archived'    => 'Archived',
    ],
];
