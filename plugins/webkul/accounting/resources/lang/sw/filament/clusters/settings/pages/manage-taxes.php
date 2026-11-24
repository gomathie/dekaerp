<?php

return [
    'title' => 'Simamia kodi',

    'form' => [
        'default-taxes' => [
            'label'       => 'Kodi chaguomsingi',
            'helper-text' => 'Chaguomsingi itatumika kwenye bidhaa kama hakuna kodi iliyochaguliwa',
        ],

        'sales-tax' => [
            'label' => 'Kodi ya mauzo',
        ],

        'purchase-tax' => [
            'label' => 'Kodi ya ununuzi',
        ],

        'prices' => [
            'label' => 'Bei',
        ],

        'rounding-method' => [
            'label'       => 'Njia ya ukadiriaji',
            'helper-text' => 'Njia inayotumika kukadiria kiasi cha kodi',

            'options' => [
                'round-per-line' => 'Kadiria kwa kila mstari',
                'round-globally' => 'Kadiria kwa jumla',
            ],
        ],

        'fiscal-country' => [
            'label' => 'Nchi ya kodi',
        ],
    ],
];
