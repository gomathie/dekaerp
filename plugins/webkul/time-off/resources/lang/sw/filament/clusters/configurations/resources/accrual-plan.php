<?php

return [
    'title'      => 'Mpango wa ulimbikizaji',
    'navigation' => [
        'title' => 'Mpango wa ulimbikizaji',
    ],

    'form' => [
        'fields' => [
            'name'                    => 'Jina',
            'is-based-on-worked-time' => 'Inategemea muda wa kazi',
            'accrued-gain-time'       => 'Wakati wa kulimbikiza',
            'carry-over-time'         => 'Muda wa kuhamisha',
            'carry-over-date'         => 'Tarehe ya kuhamisha',
            'status'                  => 'Hali',
        ],
    ],

    'table' => [
        'columns' => [
            'name'   => 'Jina',
            'levels' => 'Viwango',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Mpango wa ulimbikizaji umefutwa',
                    'body'  => 'Mpango wa ulimbikizaji umefutwa kwa mafanikio.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Mpango wa ulimbikizaji umefutwa',
                    'body'  => 'Mpango wa ulimbikizaji umefutwa kwa mafanikio.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'basic-information' => 'Taarifa za msingi',
        ],

        'entries' => [
            'name'                    => 'Jina',
            'is-based-on-worked-time' => 'Inategemea muda wa kazi',
            'accrued-gain-time'       => 'Wakati wa kulimbikiza',
            'carry-over-time'         => 'Muda wa kuhamisha',
            'carry-over-day'          => 'Siku ya kuhamisha',
            'carry-over-month'        => 'Mwezi wa kuhamisha',
        ],
    ],
];
