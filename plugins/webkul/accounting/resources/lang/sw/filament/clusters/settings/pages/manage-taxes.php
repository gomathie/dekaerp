<?php

return [
    'title' => 'Manage Kodi',

    'form' => [
        'default-taxes' => [
            'label'       => 'Chaguo-msingi Kodi',
            'helper-text' => 'Chaguo-msingi ita kuwa imetumika washa bidhaa ikiwa hakuna kodi ni imechaguliwa',
        ],

        'sales-tax' => [
            'label' => 'Mauzo Kodi',
        ],

        'purchase-tax' => [
            'label' => 'Ununuzi Kodi',
        ],

        'prices' => [
            'label' => 'Bei',
        ],

        'rounding-method' => [
            'label'       => 'Rounding Method',
            'helper-text' => 'Method inatumika hadi round kodi amounts',

            'options' => [
                'round-per-line' => 'Round Kwa Mstari',
                'round-globally' => 'Round Globally',
            ],
        ],

        'fiscal-country' => [
            'label' => 'Kifedha Nchi',
        ],
    ],
];
