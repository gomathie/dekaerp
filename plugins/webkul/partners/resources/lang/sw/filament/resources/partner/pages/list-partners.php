<?php

return [
    'title' => 'Wahusika',

    'header-actions' => [
        'create' => [
            'label' => 'Unda mhusika',
        ],

        'import' => [
            'label' => 'Leta kutoka Excel',

            'modal' => [
                'heading'                  => 'Leta wahusika kutoka jedwali',
                'description'              => 'Pakua faili la mfano, jaza mhusika mmoja kwa kila safu, kisha lipakie hapa. Hadi wahusika :max kwa kila upakiaji.',
                'submit'                   => 'Leta wahusika',
                'download-template-csv'    => 'Pakua mfano (CSV)',
                'download-template-excel'  => 'Pakua mfano (Excel)',

                'file' => [
                    'label'  => 'Faili lililojazwa',
                    'helper' => 'Excel (.xlsx, .xls) au CSV. Acha safu ya vichwa kama ilivyo. Safu wima inayohitajika: :columns. Safu zenye tatizo zitaonyeshwa kwa namba ya safu, na nyingine zitaletwa.',
                ],
            ],

            'notifications' => [
                'imported'        => 'Wahusika :count wameletwa',
                'skipped'         => 'Safu :count zimerukwa:',
                'nothing-imported' => 'Hakuna mhusika aliyeletwa',
                'more-errors'     => '... na safu nyingine :count.',
                'unreadable'      => 'Faili hilo halikuweza kusomwa. Tafadhali pakia faili la Excel au CSV.',
            ],
        ],
    ],

    'tabs' => [
        'individuals' => 'Watu binafsi',
        'companies'   => 'Kampuni',
        'employees'   => 'Wafanyakazi',
        'customers'   => 'Wateja',
        'vendors'     => 'Wasambazaji',
        'archived'    => 'Kumbukumbu',
    ],
];
