<?php

return [
    'title' => 'Mawasiliano',

    'header-actions' => [
        'create' => [
            'label' => 'Unda Mwasiliani',
        ],

        'import' => [
            'label' => 'Ingiza kutoka Excel',

            'modal' => [
                'heading'                  => 'Ingiza mawasiliano kutoka spreadsheet',
                'description'              => 'Pakua mfano faili, fill katika one mwasiliani kwa mstari, kisha pakia hii hapa. Juu hadi :max mawasiliano kwa pakia.',
                'submit'                   => 'Ingiza mawasiliano',
                'download-template-csv'    => 'Pakua mfano (CSV)',
                'download-template-excel'  => 'Pakua mfano (Excel)',

                'file' => [
                    'label'  => 'Imekamilika faili',
                    'helper' => 'Excel (.xlsx,.xls) au CSV. Weka heading mstari kama hii ni. Inahitajika safu: :columns. Mistari na problem ni reported na mstari nambari na rest ni bado imported.',
                ],
            ],

            'notifications' => [
                'imported'        => ':count mawasiliano imported',
                'skipped'         => ':count mistari zilikuwa skipped:',
                'nothing-imported' => 'Hakuna mawasiliano zilikuwa imported',
                'more-errors'     => '... na :count zaidi mistari.',
                'unreadable'      => 'Hiyo faili could si kuwa kusoma. Tafadhali pakia Excel au CSV faili.',
            ],
        ],
    ],

    'tabs' => [
        'individuals' => 'Individuals',
        'companies'   => 'Makampuni',
        'employees'   => 'Wafanyakazi',
        'customers'   => 'Wateja',
        'vendors'     => 'Wasambazaji',
        'archived'    => 'Imehifadhiwa',
    ],
];
